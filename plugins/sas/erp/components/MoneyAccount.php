<?php namespace Sas\Erp\Components;

use Cms\Classes\ComponentBase;
use Sas\Erp\Models\Account;
use Sas\Erp\Models\AccountTransaction;
use Cms\Classes\Page;
use Flash;
use Auth;
use Debugbar;
use Db;

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
                'title' => 'sas.erp::lang.account.slug',
                'description' => 'sas.erp::lang.account.slug_desc',
                'default' => '{{ :slug }}',
                'type' => 'string',
            ],
            'slugType'       => [
                'title' => 'sas.erp::lang.account.slug_type',
                'description' => 'sas.erp::lang.account.slug_type_desc',
                'default' => 'PLACE',
                'type' => 'dropdown',
                'options' => ['PLACE', 'USER']
            ],
            'id'       => [
                'title' => 'sas.erp::lang.account.id',
                'description' => 'sas.erp::lang.account.id_desc',
                'default' => '{{ :accId }}',
                'type' => 'string',
            ],
            'transactionsLimit'       => [
                'title' => 'sas.erp::lang.account.transactions_limit',
                'description' => 'sas.erp::lang.account.transactions_limit_desc',
                'default' => '30',
                'type' => 'string',
            ],
        ];
    }

    public function onRun() {
        $this->page['owner_id'] = $this->property('slug');
        $this->page['transactions_limit'] = $this->property('transactionsLimit');

        $owner_id = $this->property('slug');
        $owner = null;
        switch ($this->property('slugType')) {
            case "0":
                $owner = \Sas\Erp\Models\Place::where('code_id', $owner_id)->first();
                //$this->page['back_link'] = 'place/' . $owner_id;
                break;
            case "1":
                $owner = Auth::getUser($owner_id);
                //$this->page['back_link'] = 'profile/' . $owner_id;
                break;
        }
        if ($owner && $owner->accounts->count() > 0) {
            $this->page['account'] = $owner->accounts->first();
            $accid = $this->property('id');
            if ($accid) {
                foreach ($owner->accounts as $account) {
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
        if ((Auth::check())) {
            switch ($this->property('slugType')) {
                case "0":
                    if ((Auth::getUser()->company_id == $owner->id)) $this->page['isGuest'] = false;
                    break;
                case "1":
                    if ((Auth::getUser()->id == $owner->id)) $this->page['isGuest'] = false;
                    break;
            }
        }

        $this->getAccountReport();
    }

    public function onAddAccount() {
        $data = post();
        $new_account = new Account([
            'name' => $data['account_name'],
            'description' => $data['account_description'],
        ]);
        $owner = null;
        switch ($this->property('slugType')) {
            case "0":
                $owner = \Sas\Erp\Models\Place::where('code_id', $data['owner_id'])->first();
                break;
            case "1":
                $owner = Auth::getUser($data['owner_id']);
                break;
        }
        $new_account = $owner->accounts()->add($new_account);
        $this->page['account'] = $new_account;
        $this->page['isNew'] = false;

        Flash::success('Thông tin đã được lưu trữ thành công.');
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

        $this->getAccountReport();

        Flash::success('Thông tin đã được lưu trữ thành công.');
    }

    public function onRemoveTransaction() {
        $data = post();
        $old_transaction = AccountTransaction::where('id', $data['transaction_id'])->first();
        //$old_transaction = AccountTransaction::destroy($data['transaction_id']);

        $account = Account::where('id', $data['account_id'])->first();
        $account->balance = $account->balance - $old_transaction->amount;
        $account->transactions()->remove($old_transaction);
        $account->save();
        $this->page['account'] = $account;

        $old_transaction->delete();

        $this->getAccountReport();

        Flash::success('Dữ liệu đã được cập nhật thành công.');
    }

    public function getUserOptions() {
        return RainLab\User\Models\User::lists('name', 'id');
    }

    protected function getAccountReport() {
        $report_transactions = AccountTransaction::where('account_id', $this->property('id'))
            ->join('users', 'user_id', '=', 'users.id')
            ->select('users.name', Db::raw('SUM(amount) as total_amount'))
            ->groupBy('user_id')
            ->get();
        $this->page['report_transactions'] = $report_transactions;
    }
}
