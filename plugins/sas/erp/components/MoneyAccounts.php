<?php namespace Sas\Erp\Components;

use Redirect;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;

class MoneyAccounts extends ComponentBase {
    public $pageParam;

    public function componentDetails() {
        return [
            'name'        => 'sas.erp::lang.plugin.accounts',
            'description' => 'List of accounts'
        ];
    }

    public function defineProperties() {
        return [
            'pageNumber' => [
                'title'       => 'sas.erp::lang.settings.accounts_pagination',
                'description' => 'sas.erp::lang.settings.accounts_pagination_description',
                'type'        => 'string',
                'default'     => '{{ :page }}',
            ],
            'accountsPerPage' => [
                'title'             => 'sas.erp::lang.settings.accounts_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'sas.erp::lang.settings.accounts_per_page_validation',
                'default'           => '10',
            ],
            'noAccountsMessage' => [
                'title'        => 'sas.erp::lang.settings.accounts_no_places',
                'description'  => 'sas.erp::lang.settings.accounts_no_places_description',
                'type'         => 'string',
                'default'      => 'No item found',
                'showExternalParam' => false
            ],
            'accountPage' => [
                'title'       => 'sas.erp::lang.settings.accounts_place',
                'description' => 'sas.erp::lang.settings.accounts_place_description',
                'type'        => 'dropdown',
                'default'     => 'account',
            ],
        ];
    }

    public function getAccountPageOptions() {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun() {
        $this->prepareVars();

        $accounts = $this->page['accounts'] = $this->listAccounts();

        /*
         * If the page number is not valid, redirect
         */
        if ($pageNumberParam = $this->paramName('pageNumber')) {
            $currentPage = $this->property('pageNumber');

            if ($currentPage > ($lastPage = $accounts->lastPage()) && $currentPage > 1)
                return Redirect::to($this->currentPageUrl([$pageNumberParam => $lastPage]));
        }
    }

    protected function prepareVars() {
        $this->pageParam = $this->page['pageParam'] = $this->paramName('pageNumber');
        $this->page['noAccountsMessage'] = $this->property('noAccountsMessage');

        /*
         * Page links
         */
        $this->page['accountPage'] = $this->property('accountPage');
    }

    protected function listAccounts() {
        /*
         * List all the posts, eager load their categories
         */
        $accounts = \Sas\Erp\Models\Account::listFrontEnd([
            'page'       => $this->property('pageNumber'),
            'perPage'    => $this->property('accountsPerPage'),
            'search'     => trim(input('search'))
        ]);

        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $accounts->each(function($account) {
            $account->setUrl($this->property('accountPage'), $this->controller);
        });

        return $accounts;
    }
}
