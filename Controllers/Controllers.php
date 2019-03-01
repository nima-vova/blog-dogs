<?php
/**
 * Created by PhpStorm.
 * User: nima
 * Date: 17.04.18
 * Time: 20:23
 */

namespace Controllers;

//use Faker;
use Tests\LoadFakeData;

/**
 * Class Controllers
 * @package Controllers
 */
class Controllers extends AbstractControllers
{
    /**
    * Controllers constructor.
    */
    public function __construct()
    {
        parent::__construct();
        //$this->faker = new Faker\Generator();
        //    $this->faker = Faker\Factory::create();

        $this->loadDate = new LoadFakeData();
    }

    /**
     * @return void
     */
    public function indexAction(): void
    {
        $publications = $this->publicationsSelect->getPublications();
        if (isset($_POST['object-show']) && $_POST['object-show']=="ajax") {
            //var_dump($publications);
            header('Content-type: application/json');
            echo json_encode($publications);
        } else {
            //   echo $this->twigAdmin->render('main-page-admin.html', array('param' => json_encode($allUsers), 'menu'=>'users-show'));
            echo $this->twig->render('main-page.html', array('menu' => 'main-page', 'data' => json_encode($publications)));
        }
    }

    /**
     * @return void
     */
    public function showPublicationsById(): void
    {
        // перевіряєм чи існує в кінці url іd цілочисельний
        // і підходить критеріям (в сервісі urlAnalysis->defineIndexinUrl()).
        $defineIndexinUrl = $this->urlAnalysis->defineIndexinUrl();
        if ($defineIndexinUrl) {
            // запит до бази - вибірка публікації по id
            $publication = $this->publicationsSelect->getPublicationById($defineIndexinUrl);
            if ($publication) {
                // запит до бази - вибірка назв тегів які відносяться до вибраної публікації
                $getTagsNamesByIdPublication = $this->publicationsSelect->getTagsNamesByIdPublication($defineIndexinUrl);
                // додається до обекта публікація (властивостями якого є поля з таблиці із значеннями)
                // властивість ->tags(де міститься масив значень імен тегів, які відносятья до цієї публікації())
                $publication->tags = $getTagsNamesByIdPublication;
                // перевіряєтья з відки прийшов запит. якщо ajax, то передаєм публікацію знайдену по id
                //(в js функції ajax яка визвала цей роут), або повідомлення що не знайшлась публікація
                if (isset($_POST['object-show']) && $_POST['object-show'] == "ajax") {
                    //var_dump($publications);
                    header('Content-type: application/json');
                    echo json_encode($publication);
                } // якщо не аякс то передаєм в twig публікацію знайдену по id, назву меню (роут) до якого відносяться дані для виведення
                else {
                    echo $this->twig->render('main-page.html', array('menu' => 'publications-show-' . $publication->id, 'data' => json_encode($publication)));
                }
            }
            else {
                // url іd не ічнує(не ма публікації по цьому id),
                // виводидим що сторінка не існує
                $this->notFoundAction();
            }
        }
        // url іd не підходить критеріям (в сервісі urlAnalysis->defineIndexinUrl()),
        // виводидим що сторінка не існує
        else {
            $this->notFoundAction();
        }
    }
    // для роута, який виводить публікації по вибраному тегу
    /**
     * @return void
     */
    public function showTags(): void
    {
        //var_dump($_POST);
        // вибирається з url назва тега
        $tagName = $this->urlAnalysis->lastElemUrl;
        //var_dump($tagName);
        $getPublicationsByTagName = $this->publicationsSelect->getPublicationsByTagName($tagName);
        //var_dump($getPublicationsByTagName);
        if (isset($_POST['object-show']) && $_POST['object-show']=="ajax") {
            header('Content-type: application/json');
            echo json_encode($getPublicationsByTagName);
        } else {
            echo $this->twig->render('main-page.html', array('menu' => 'tags-show-'.$tagName, 'data' => json_encode($getPublicationsByTagName)));
        }
    }

    /**
    * @return void
    */
    public function loadFakeAction(): void
    {
        $loadData = $this->loadDate->testLoadData();

        // не видалять
        //echo $this->twig->render('main-page-admin.html', array('menu' => $loadData,));
        echo $this->twig->render('main-page.html', array('menu' => $loadData,));
    }
}
