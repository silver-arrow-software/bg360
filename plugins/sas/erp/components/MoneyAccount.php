<?php namespace Sas\Erp\Components;

use Cms\Classes\ComponentBase;
use Sas\Erp\Models\Account;
use Sas\Erp\Models\AccountTransaction;
use Cms\Classes\Page;
use Flash;
use Auth;

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
        ];
    }

    public function onRun() {
        $this->page['owner_id'] = $this->property('slug');

        $placeid = $this->property('slug');
        $place = \Sas\Erp\Models\Place::where('code_id', $placeid)->first();
        //$this->page['accounts'] = $place->accounts;
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
        $this->page['isGuest'] = true;
        if ((Auth::check()) && (Auth::getUser()->company_id == $place->id)) $this->page['isGuest'] = false;
    }

    public function onAddTransaction() {
        $data = post();
        $creator = Auth::getUser();

        $account = Account::where('id', $data['account_id'])->first();
        $account->balance = $account->balance + $data['amount'];
        $new_transaction = new AccountTransaction([
            'user_id' => $creator->id,
            'description' => $data['description'],
            'account_id' => $data['account_id'],
            'amount' => $data['amount'],
            //'created_at' => $data['modified_at_submit']
        ]);
        //$account->transactions()->save($new_transaction);
        $account->save();
        $new_transaction->save();
        $this->page['account'] = $account;

        Flash::success('Thông tin đã được lưu trữ thành công.');
    }

    public function getUserOptions() {
        return RainLab\User\Models\User::lists('name', 'id');
    }
}
