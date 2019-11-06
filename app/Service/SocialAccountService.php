<?php
namespace App\Services;

use App\User;
use App\SocialAccount;
use Laravel\Socialite\Contracts\Provider;


class SocialAccountService
{
    public function createOrGetUser(Provider $provider)
    {
        $providerUser = $provider->user();
        $providerName = class_basename($provider);//Lấy về tên của class

        $account = SocialAccount::whereProvider($providerName)
            ->whereProviderUserId($providerUser->getId())
            ->first();//Kiểm tra tài khoản này đã tồn tại chưa

        if ($account) {
            $t = $account->user->update([
                'name' => $providerUser->getName(),
                'avatar' => $providerUser->getAvatar(),
            ]);

            return $account->user;//Nếu đã tồn tại lấy ra thông tin của user
        } else {
            $account = new SocialAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => $providerName
            ]);//Tạo tài khoản ở bảng social_accounts

            $user = User::whereEmail($providerUser->getEmail())->first();
            //Kiểm tra xem email này đã từng tồn tại trong bảng user chưa.
            if (!$user) {
                $user = User::create([
                    'email' => $providerUser->getEmail(),
                    'name' => $providerUser->getName(),
                    'avatar' => $providerUser->getAvatar(),
                ]);
            }//Nếu chưa thì cập nhập các trường thông tin vào bảng user

            $account->user()->associate($user);
            $account->save();

            return $user;
        }
    }
}
?>
