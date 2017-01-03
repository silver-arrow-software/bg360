<?php namespace Sas\Erp\Components;

use Cms\Classes\ComponentBase;
use Sas\Erp\Models\Account;
use Sas\Erp\Models\AccountTransaction;
use Cms\Classes\Page;
use Flash;
use Auth;
use Debugbar;

class MoneyAccount extends ComponentBase {
    public function componentDetails() {
        return [
            'name'        => 'Account Component',
            'description' => 'Details of one money account.'
        ];
    }

    public function defineProperties() {
        return [
            'slug'       => [
                'title' => 'sas.erp::lang.account.place_slug',
                'description' => 'sas.erp::lang.account.place_slug_desc',
                'default' => '{{ :placeSlug }}',
                'type' => 'string',
            ],
            'id'       => [
                'title' => 'sas.erp::lang.account.id',
                'description' => 'sas.erp::lang.account.id_desc',
                'default' => '{{ :accId }}',
                'type' => 'string',
            ],
            'transactions_limit'       => [
                'title' => 'sas.erp::lang.account.transactions_limit',
                'description' => 'sas.erp::lang.account.transactions_limit_desc',
                'default' => '30',
                'type' => 'string',
            ],
        ];
    }

    public function onRun() {
        $this->page['owner_id'] = $this->property('slug');
        $this->page['transactions_limit'] = $this->property('transactions_limit');

        $placeid = $this->property('slug');
        $place = \Sas\Erp\Models\Place::where('code_id', $placeid)->first();
        //$this->page['accounts'] = $place->accounts;
        if ($place->accounts->count() > 0) {
            $this->page['account'] = $place->accounts->first();
            $accid = $this->property('id');
            if ($accid) {
                foreach ($place->accounts as $account) {
                    if ($account->id == $accid) {
                        $this->page['account'] = $account;
                        break;
                    }
                }
            }
            $this->page['isNew'] = false;
        } else {
            //tạo mới money account cho place
            $this->page['isNew'] = true;
        }
        $this->page['isGuest'] = true;
        if ((Auth::check()) && (Auth::getUser()->company_id == $place->id)) $this->page['isGuest'] = false;
    }

    public function onAddTransaction() {
        $creator = Auth::getUser();
        $data = post();
        $amount = str_replace(".", "", $data['amount']);

        $account = Account::where('id', $data['account_id'])->first();
        $new_transaction = new AccountTransaction([
            'user_id' => $creator->id,
            'description' => $data['description'],
            'account_id' => $data['account_id'],
            'amount' => $amount,
            //'created_at' => $data['modified_at_submit']
        ]);
        $account->transactions()->add($new_transaction);

        //$account->transactions()->save($new_transaction);
        $account->balance = $account->balance + $amount;
        $account->save();

        if (strlen($data['modified_at_submit']) > 0) {
            $new_transaction->created_at = $data['modified_at_submit'];
            $new_transaction->save();
        }

        $this->page['account'] = $account;

        Flash::success('Thông tin đã được lưu trữ thành công.');
    }

    public function onAddAccount() {
        $data = post();
        $new_account = new Account([
            'name' => $data['account_name'],
            'description' => $data['account_description'],
        ]);
        $owner = \Sas\Erp\Models\Place::where('code_id', $data['place_code'])->first();
        $new_account = $owner->accounts()->add($new_account);
        $this->page['account'] = $new_account;
        $this->page['isNew'] = false;

        Flash::success('Thông tin đã được lưu trữ thành công.');
    }

    public function getUserOptions() {
        return RainLab\User\Models\User::lists('name', 'id');
    }
}
