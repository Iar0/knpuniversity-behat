<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;

require_once __DIR__.'/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context
{
    use KernelDictionary;
    private $currentUser;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @When I fill in the search box with :term
     */
    public function iFillInTheSearchBoxWith($term)
    {
        $searchBox = $this->getPage()
            ->find('css', 'input[name="searchTerm"]');

        assertNotNull($searchBox, 'Could not find the search box');

        $searchBox->setValue($term);
    }

    /**
     * @When I press the search button
     */
    public function iPressTheSearchButton()
    {
        $searchButton = $this->getPage()
            ->find('css', '#search_submit');

        assertNotNull($searchButton, 'Could not find the search button');

        $searchButton->press();
    }

    /**
     * @Given there is an admin user with name :username and password :password
     */
    public function thereIsAnAdminUserWithNameAndPassword($username, $password)
    {
        $user = new \AppBundle\Entity\User();
        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setRoles(array('ROLE_ADMIN'));

        $em = $this->getEntityManager();

        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     *@BeforeScenario
     */
    public function resetData()
    {
        $em = $this->getEntityManager();

        //$em->createQuery('DELETE FROM AppBundle:User')->execute();
        $purger = new \Doctrine\Common\DataFixtures\Purger\ORMPurger($em);
        $purger->purge();
    }

    /**
     * @BeforeScenario @fixtures
     */
    public function loadFixtures()
    {
        $loader = new \Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader($this->getContainer());
        $loader->loadFromDirectory(__DIR__ . '/../../src/AppBundle/DataFixtures');
        $executor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor($this->getEntityManager());
        $executor->execute($loader->getFixtures(), true);
    }

    /**
     * @Given there are :count products
     */
    public function thereAreProducts($count)
    {
        $this->createProducts($count);
    }

    /**
     * @Given I author :count products
     */
    public function iAuthorProducts($count)
    {
        $this->createProducts($count, $this->currentUser);
    }

    private function createProducts($count, \AppBundle\Entity\User $author = null)
    {
        $em = $this->getEntityManager();

        for($i = 0; $i < $count; $i++) {
            $product = new \AppBundle\Entity\Product();
            $product->setName("Product $i");
            $product->setPrice(rand(10, 1000));
            $product->setDescription("Description $i");

            if ($author) {
                $product->setAuthor($author);
            }

            $em->persist($product);
        }

        $em->flush();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @When I click :linkText
     */
    public function iClick($linkText)
    {
        $this->getPage()->clickLink($linkText);
    }

    /**
     * @Then I should see :count products
     */
    public function iShouldSeeProducts($count)
    {
        $table = $this->getPage()->find('css', 'table.table');

        assertNotNull($table, 'Cannot find a table!');

        assertCount(intval($count), $table->findAll('css', 'tbody tr'));
    }


    /**
     * @Given I am logged in as an admin
     */
    public function iAmLoggedInAsAnAdmin()
    {
        $name = 'admin';
        $pw = 'admin';
        $this->currentUser = $this->thereIsAnAdminUserWithNameAndPassword($name, $pw);

        $this->visitPath('/login');
        $this->getPage()->fillField('Username', $name);
        $this->getPage()->fillField('Password', $pw);
        $this->getPage()->pressButton('Login');

    }


    /**
     * @return \Behat\Mink\Element\DocumentElement
     */
    private function getPage()
    {
        return $this->getSession()->getPage();
    }
}
