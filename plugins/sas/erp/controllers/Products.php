<?php namespace Sas\Erp\Controllers;

use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use Sas\Erp\Models\Product;
use Debugbar;
use System\Models\File;
use Storage;

/**
 * Products Back-end Controller
 */
class Products extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = ['sas.erp.manage_products'];

    public function __construct()
    {
        parent::__construct();

		BackendMenu::setContext('Sas.Erp', 'sas-erp-main-menu-item', 'sas-erp-side-menu-products');
    }

    public function index()
    {
        $this->vars['productsTotal'] = Product::count();
        $this->vars['productsPublished'] = Product::isPublished()->count();
        $this->vars['productsDrafts'] = $this->vars['productsTotal'] - $this->vars['productsPublished'];

        $this->asExtension('ListController')->index();
    }

    public function create()
    {
        $this->addCss('/plugins/sas/erp/assets/css/sas.erp.css');
        return $this->asExtension('FormController')->create();
    }

    public function update($recordId = null)
    {
        $this->addCss('/plugins/sas/erp/assets/css/sas.erp.css');
        return $this->asExtension('FormController')->update($recordId);
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $productId) {
                if ((!$product = Product::find($productId)) || !$product->canEdit($this->user))
                    continue;

                $product->delete();
            }

            Flash::success(sas.erp::lang.message.delete_success);
        }

        return $this->listRefresh();
    }

    public function index_onShow()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $productId) {
                if ((!$product = Product::find($productId)) || !$product->canEdit($this->user))
                    continue;

                $product->published = 1;
                $product->save();
            }

            Flash::success(sas.erp::lang.message.show_success);
        }

        return $this->listRefresh();
    }

    public function index_onHide()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $productId) {
                if ((!$product = Product::find($productId)) || !$product->canEdit($this->user))
                    continue;

                $product->published = 0;
                $product->save();
            }

            Flash::success(sas.erp::lang.message.hide_success);
        }

        return $this->listRefresh();
    }

    public function formBeforeCreate($model)
    {
        $model->user_id = $this->user->id;
    }

    public function import() {
        //$this->importFromFile();
    }

    protected function importFromFile() {
        $url = temp_path('collections.xml');
        $xmlColl = \simplexml_load_file($url);
        $client = new \GuzzleHttp\Client();
        foreach ($xmlColl->item as $xmlItem) {
            $newItem = $this->convertXmlItem($xmlItem);
            $newSlug = str_slug($newItem['name'], '-');
            if (Product::where('slug', $newSlug)->count() <= 0 ) {
                //$boardgame = $client->request('GET', 'https://www.boardgamegeek.com/xmlapi2/thing?id='.$newItem['bgg_id'].'&stats=1');dd((string)$boardgame->getBody());
                //$boardgame = $client->request('GET', 'http://www.boardgamegeek.com/xmlapi/boardgame/'.$newItem['bgg_id'].'&stats=1');dd((string)$boardgame->getBody());
                $xml = \simplexml_load_file('https://www.boardgamegeek.com/xmlapi2/thing?id='.$newItem['bgg_id'].'&stats=1');
                $newItem['description'] = $xml->item->description;
                $newItem['year_published'] = $xml->item->yearpublished['value'];
                $newItem['age'] = $xml->item->minage['value'];
                try {
                    $filepath = 'http:'.$newItem['bgg_image_url'];
                    $filename = '/boardgame/' . basename($filepath);
                    $image = $client->request('GET', $filepath, ['synchronous' => true]);
                    Storage::put('uploads'.$filename, $image->getBody()->getContents());
                } catch (Exception $e) {
                }
                $filename = storage_path('app/uploads' . $filename);

                $newProduct = new Product([
                    'title' => $newItem['name'],
                    'user_id' => 1,
                    'excerpt' => $this->createIntroText($newItem),
                    'description' => $newItem['description'],
                    'slug' => $newSlug,
                    'hash_id' => uniqid(),
                    'options' => json_encode($newItem)
                ]);
                $newProduct->save();
                $newProduct->tags()->attach([6, 13]);   //boardgame, cardgame
                $newProduct->images()->create(['data' => $filename]);
            }
        }
    }

    protected function convertXmlItem(\SimpleXMLElement $xmlItem) {
        $arrayItem = array(
            'bgg_id' => (int) $xmlItem['objectid'],
            'name' => (string) $xmlItem->name,
            'bgg_image_url' => (string) $xmlItem->image,
            'bgg_thumbnail_url' => (string) $xmlItem->thumbnail,
            'min_players' => (int) $xmlItem->stats['minplayers'],
            'max_players' => (int) $xmlItem->stats['maxplayers'],
            'min_playtime' => (int) $xmlItem->stats['minplaytime'],
            'max_playtime' => (int) $xmlItem->stats['maxplaytime'],
            'playing_time' => (int) $xmlItem->stats['playingtime'],
            'rating' => null,
            'stats' => null,
            'ranks' => null,
        );

        if ($xmlItem->stats) {
            $rating = $xmlItem->stats->rating;

            $arrayItem['rating'] = array(
                'users_rated' => (int) $rating->usersrated['value'],
                'average' => (float) $rating->average['value'],
                'bayes_average' => (float) $rating->bayesaverage['value'],
                'std_dev' => (float) $rating->stddev['value'],
                'median' => (float) $rating->median['value'],
            );

            $ranks = array();
            foreach ($rating->ranks->rank as $rank) {
                $value = (int) $rank['value'];
                if ($value === 0) {
                    $value = null;
                }

                $ranks[] = array(
                    'type' => (string) $rank['type'],
                    'id' => (int) $rank['id'],
                    'name' => (string) $rank['name'],
                    'friendly_name' => (string) $rank['friendlyname'],
                    'value' => $value,
                    'bayes_average' => (float) $rank['bayesaverage'],
                );
            }
            $arrayItem['ranks'] = $ranks;

            $arrayItem['stats'] = array(
                'owned' => (int) $xmlItem->status['own'],
                'trading' => (int) $xmlItem->status['fortrade'],
                'wanting' => (int) $xmlItem->status['want'],
                'wishing' => (int) $xmlItem->status['wishlist'],
                'wanttoplay' => (int) $xmlItem->status['wanttoplay'],
                'wanttobuy' => (int) $xmlItem->status['wanttobuy'],
                'lastmodified' => new \DateTime((string) $xmlItem->status['lastmodified']),
            );
        }

        return $arrayItem;
    }

    // protected function importFromFile() {
    //     //$url = temp_path('bgg-snapshot-20080607.xml');
    //     for ($i = 1; $i <= 200; $i++) {
    //         $url = temp_path('bgg/' . $i . '.xml');
    //         $xml = \simplexml_load_file($url);
    //         foreach ($xml->boardgame as $xmlItem) {
    //             $newItem = $this->convertXmlItem($xmlItem);
    //             if ($newItem['year_published'] < 2012) continue;
    //             $newSlug = str_slug($newItem['name'], '-');
    //             if (Product::where('slug', $newSlug)->count() <= 0 ) {
    //                 $newProduct = new Product([
    //                     'title' => $newItem['name'],
    //                     'user_id' => 1,
    //                     'excerpt' => $this->createIntroText($newItem),
    //                     'description' => $newItem['description'],
    //                     'slug' => $newSlug,
    //                     'hash_id' => uniqid(),
    //                     'options' => json_encode($newItem)
    //                 ]);
    //                 $newProduct->save();
    //                 $newProduct->tags()->attach([6, 13]);   //boardgame, cardgame
    //             }
    //         }
    //     }
    // }
    //
    // protected function convertXmlItem(\SimpleXMLElement $xmlItem) {
    //     $arrayItem = array(
    //         'bgg_id' => (int) $xmlItem['objectid'],
    //         'name' => (string) $xmlItem->name[0],
    //         'bgg_image_url' => (string) $xmlItem->image,
    //         'bgg_thumbnail_url' => (string) $xmlItem->thumbnail,
    //         'year_published' => (int) $xmlItem->yearpublished,
    //         'min_players' => (int) $xmlItem->minplayers,
    //         'max_players' => (int) $xmlItem->maxplayers,
    //         'playing_time' => (int) $xmlItem->playingtime,
    //         'age' => (int) $xmlItem->age,
    //         'description' => (string) $xmlItem->description,
    //         'rating' => null,
    //         'stats' => null,
    //         'ranks' => null,
    //         'boardgame_honor' => (array) $xmlItem->boardgamehonor,
    //         'boardgame_publisher' => (array) $xmlItem->boardgamepublisher,
    //         'boardgame_version' => (array) $xmlItem->boardgameversion,
    //         'boardgame_family' => (array) $xmlItem->boardgamefamily,
    //         'boardgame_category' => (array) $xmlItem->boardgamecategory,
    //         'boardgame_mechanic' => (array) $xmlItem->boardgamemechanic,
    //         'boardgame_artist' => (array) $xmlItem->boardgameartist,
    //         'boardgame_designer' => (array) $xmlItem->boardgamedesigner,
    //         'boardgame_subdomain' => (array) $xmlItem->boardgamesubdomain,
    //     );
    //
    //     if ($xmlItem->statistics) {
    //         $stats = $xmlItem->statistics;
    //         $rating = $stats->ratings;
    //
    //         $arrayItem['rating'] = array(
    //             'users_rated' => (int) $rating->usersrated,
    //             'average' => (float) $rating->average,
    //             'bayes_average' => (float) $rating->bayesaverage,
    //             'std_dev' => (float) $rating->stddev,
    //             'median' => (float) $rating->median,
    //         );
    //
    //         $ranks = array();
    //         foreach ($rating->ranks->rank as $rank) {
    //             $value = (int) $rank['value'];
    //             if ($value === 0) {
    //                 $value = null;
    //             }
    //
    //             $ranks[] = array(
    //                 'type' => (string) $rank['type'],
    //                 'id' => (int) $rank['id'],
    //                 'name' => (string) $rank['name'],
    //                 'friendly_name' => (string) $rank['friendlyname'],
    //                 'value' => $value,
    //                 'bayes_average' => (float) $rank['bayesaverage'],
    //             );
    //         }
    //         $arrayItem['ranks'] = $ranks;
    //
    //         $arrayItem['stats'] = array(
    //             'owned' => (int) $rating->owned,
    //             'trading' => (int) $rating->trading,
    //             'wanting' => (int) $rating->wanting,
    //             'wishing' => (int) $rating->wishing,
    //             'num_comments' => (int) $rating->numcomments,
    //             'num_weights' => (int) $rating->numweights,
    //             'average_weight' => (float) $rating->averageweight,
    //         );
    //     }
    //
    //     return $arrayItem;
    // }

    protected function createIntroText(array $xmlItem) {
        $excerpt = 'Số người chơi: ' . $xmlItem['min_players'] . ' - ' . $xmlItem['max_players'] . '.<br/>';
        $excerpt .= 'Phù hợp với ' . $xmlItem['age'] . ' tuổi trở lên.<br/>';
        $excerpt .= 'Thời gian chơi khoảng ' . $xmlItem['playing_time'] . ' phút.<br/>';
        $excerpt .= 'Năm sản xuất: ' . $xmlItem['year_published'] . '.';

        return $excerpt;
    }
}
