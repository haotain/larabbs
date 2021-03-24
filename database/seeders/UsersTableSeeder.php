<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(10)->create();

        $user = User::find(1);
        $user->name = 'wsy';
        $user->email = '872789604@qq.com';
        $user->password = '$2y$10$r9J68VsR9kN1.AKZV3DC3OHNh0Vt1UKoMf6A2d5ItwUroSxFn/aKu';
        $user->avatar = 'http://www.bbs.com/uploads/images/avatars/202103/18/1_1616073658_aIKpNFU6GK.jpg';
        $user->save();

        // 初始化用户角色，将 1 号用户指派为『站长』
        $user->assignRole('Founder');

        // 将 2 号用户指派为『管理员』
        $user = User::find(2);
        $user->assignRole('Maintainer');
    }
}
