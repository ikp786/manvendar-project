<?php


namespace App\library {


    class Email
    {
        public function is_ok($id) {
            return $id;
        }
        public function billing($user, $data, $amount)
        {
            $subject = "Added Rs.$amount successfully";
            return \Mail::send('email.billing', $data, function ($m) use ($user, $subject)   {
                $m->from('Admin@payjst.in, 'Billing');
                $m->to($user['email'], $user['name'])->subject($subject);
            });
        }
        public function payment_request($user, $data, $amount){
            $subject = "Added Rs.$amount successfully";
            return \Mail::send('email.billing', $data, function ($m) use ($user, $subject)   {
                $m->from('Admin@payjst.in', 'Request');
                $m->to($user['email'], $user['name'])->subject($subject);
            });
        }

    }
}