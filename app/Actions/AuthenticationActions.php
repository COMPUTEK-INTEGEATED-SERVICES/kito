<?php


namespace App\Actions;



use App\Models\PasswordReset;
use App\Models\RegistrationVerification;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\Auth\EmailVerificationNotification;
use App\Notifications\Auth\PasswordResetNotification;
use App\Notifications\WelcomeAboardNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AuthenticationActions
{
    /**
     * @throws \Exception
     */
    public static function login($request): string
    {

        if (!auth()->attempt($request->all()))
        {
            throw new \Exception('Invalid credentials', 400);
        }

        if (auth()->user()->email_verified_at == null)
        {
            throw new \Exception('Please verify your email address to continue', 403);
        }

        return auth()->user()->createToken('basic-access-token')->plainTextToken;
    }

    /**
     * @throws \Exception
     */
    public static function register($request): bool
    {
        DB::beginTransaction();
        try {
            //create user
            $user = User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'phone'=>$request->phon,
                'password'=>Hash::make($request->password),
                'state_id'=>$request->state_id,
                'city_id'=>$request->city_id
            ]);

            //create wallet
            Wallet::create([
                'model_id'=>$user->id,
                'model_type'=>"App\Models\User"
            ]);

            $token = Str::random(10);
            //create registration verification
            RegistrationVerification::create([
                'user_id'=>$user->id,
                'email_token'=>Hash::make($token)
            ]);

            //todo send a welcome email
            $user->notify( new WelcomeAboardNotification());

            //todo send an otp for email verification
            $user->notify(new EmailVerificationNotification($token));

            DB::commit();
            return true;
        }catch (\Throwable $throwable){
            DB::rollBack();
            report($throwable);
            throw new \Exception('An error occurred please try again', 400);
        }
    }

    /**
     * @throws \Exception
     */
    public static function sendPasswordResetToken($request)
    {
        $token = Str::upper(Str::random(8));
        $tokenHash = Hash::make($token);

        try {
            PasswordReset::updateOrCreate(
                ['email' =>  request('email')],
                ['token' => $tokenHash]
            );

            $user = User::where('email', $request->email)->first();
            Notification::send($user, new PasswordResetNotification($token));
        }catch (\Throwable $throwable){
            report($throwable);
            throw new \Exception('Sorry an error occurred, please try again', 400);
        }
    }

    /**
     * @throws \Exception
     */
    public static function submitPasswordResetToken($request): bool
    {
        try {
            $token = PasswordReset::where('email', $request->email)
                ->where('token', $request->token)
                ->where('updated_at', 'created_at')
                ->first();
            if ($token)
            {
                $user = User::where('email', $token->email)->first();
                $user->password = Hash::make($request->password);
                $user->save();
                return true;
            }

            throw new \Exception('Expired token', 400);
        }catch (\Throwable $throwable){
            report($throwable);
            throw new \Exception('An error occurred, please try again', 400);
        }
    }

    /**
     * @throws \Exception
     */
    public static function verifyRegistrationOtp($request): bool
    {
        try {
            $user = User::where('email', $request->email)->first();
            $otp = RegistrationVerification::where('user_id', $user->id)
                ->where('email_token_used_at', null)
                ->first();

            if (!$otp){
                throw new \Exception('Expired token', 400);
            }
            if (!Hash::check($request->email_token, $otp->email_token))
            {
                throw new \Exception('Invalid token supplied', 400);
            }
            $user->email_verified = 1;
            $otp->email_token_used_at = Carbon::now();
            $otp->save();
            $user->save();
            return true;
        }catch (\Throwable $throwable){
            report($throwable);
            throw new \Exception('Sorry an error occurred, please try again', 400);
        }
    }

    /**
     * @throws \Exception
     */
    public static function resendEmailToken($request): bool
    {
        DB::beginTransaction();
        try {
            $user = User::where('email', $request->email)->first();
            if ($request->new_email)
            {
                $user->email = $request->email;
            }
            $user->save();
            $verification = RegistrationVerification::firstOrCreate([
                'user_id'=>$user->id
            ]);
            $otp = Str::random(10);
            //send verification code to email
            $verification->email_token = Hash::make($otp);

            $user->notify(new EmailVerificationNotification($otp));
            $verification->save();
            DB::commit();
            return true;
        }catch (\Throwable $throwable){
            DB::rollBack();
            report($throwable);
            throw new \Exception('An error occurred, please try again', 400);
        }
    }
}
