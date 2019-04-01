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
//use Symfony\Component\Yaml\Yaml;
use Config\CreateYML;
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
        // check whether the end of the url is integer id
        // and meets the criteria (in the service urlAnalysis->defineIndexinUrl()).
        $defineIndexinUrl = $this->urlAnalysis->defineIndexinUrl();
        if ($defineIndexinUrl) {
            // request to the database - the sample of publication by id
            $publication = $this->publicationsSelect->getPublicationById($defineIndexinUrl);
            if ($publication) {
                // request to the database - a selection of tag names related to the selected publication
                $getTagsNamesByIdPublication = $this->publicationsSelect->getTagsNamesByIdPublication($defineIndexinUrl);
                // the publication is added to the object (the properties of which are fields from the table with values)
                // property -> tag (which contains an array of values of tag names that refer to this post ())
                $publication->tags = $getTagsNamesByIdPublication;
                // checked out the request from the checkout. if ajax, then pass the publication found by id
                // (in js the ajax function that triggered this rout) or a message that was not published
                if (isset($_POST['object-show']) && $_POST['object-show'] == "ajax") {
                    //var_dump($publications);
                    header('Content-type: application/json');
                    echo json_encode($publication);
                }
                // if not ayax then you pass in the twig publication found by id, the menu name (rout) to which the data refer to the output
                else {
                    echo $this->twig->render('main-page.html', array('menu' => 'publications-show-' . $publication->id, 'data' => json_encode($publication)));
                }
            }
            else {
                // url ıd does not exist (there is no post on this id),
                // display that the page does not exist
                $this->notFoundAction();
            }
        }
        // url id does not fit the criteria (in the service url Analysis->defineIndexinUrl()),
        // deducing that the page does not exist
        else {
            $this->notFoundAction();
        }
    }

    // for a rout that displays posts for the selected tag
    /**
     * @return void
     */
    public function showTags(): void
    {
        //var_dump($_POST);
        // is selected from the url tag name
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

        // do not delete
        //echo $this->twig->render('main-page-admin.html', array('menu' => $loadData,));
        echo $this->twig->render('main-page.html', array('menu' => $loadData,));
    }
}
