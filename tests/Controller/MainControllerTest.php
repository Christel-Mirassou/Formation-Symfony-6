<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class MainControllerTest extends WebTestCase
{
    //TEST 1

    // public function testSomething(): void
    // {
    //     //Création d'un client cad d'un navigateur
    //     $client = static::createClient();
    //     //On demande à notre client d'aller sur la page d'accueil et faire une request GET  
    //     //le crawler et le document HTML de la page testée, il n'est pas nécessaire
    //     $crawler = $client->request('GET', '/');

    //     //On vérifie que la page d'accueil est bien affichée avec un code 200
    //     $this->assertResponseIsSuccessful();
    //     // $this->assertSelectorTextContains('h1', 'Hello World');

    //     //Ici on test que l'on reçoit un code 302 à  l'affichage de la page (quie ne doit pas marcher)
    //     // $this->assertResponseStatusCodeSame(302);

    // }

    //TEST 2

        // public function providerPublicUrlAndStatusCodes()
    // {
    //     return [
    //         'index' => ['/', 200],
    //         'contact' => ['/contact', 200],
    //         'toto' => ['/toto', 404],
    //     ];
    // }

    //Même fonction que la précédente mais en utilisant un Generator et non pas un tableau
    // public function providerPublicUrlAndStatusCodes(): \Generator
    // {
    //     yield 'index' => ['/', 200];
    //     yield 'contact' => ['/contact', 200];
    //     yield 'toto' => ['/toto', 404];
    // }

    // /**
    //  * @dataProvider providerPublicUrlAndStatusCodes
    //  */
    // public function testPublicUrlAreSuccessful(string $url, int $statusCode): void
    // {
    //     $client = static::createClient();
    //     $client->request('GET', $url);

    //     $this->assertResponseStatusCodeSame($statusCode);
    // }

    //TEST 3 sur plusieurs routes
    
    private static KernelBrowser $client;

    public static function setUpBeforeClass(): void
    {
        static::$client = static::createClient();
    }

    /**
     * @dataProvider providePublicUrlsAndStatusCodes
     */
    public function testPublicUrlIsNotServerError(string $method, string $url): void
    {
        static::$client->request($method, $url);
        if (\in_array(static::$client->getResponse()->getStatusCode(), [301, 302, 307, 308])) {
            static::$client->followRedirect();
        }

        $this->assertSame(200, static::$client->getResponse()->getStatusCode());
    }

    public function providePublicUrlsAndStatusCodes(): \Generator
    {
        $router = static::getContainer()->get(RouterInterface::class);
        $collection = $router->getRouteCollection();
        static::ensureKernelShutdown();

        foreach ($collection as $routeName => $route) {
            /** @var Route $route */
            $variables = $route->compile()->getVariables();
            if (count(array_diff($variables, $route->getDefaults())) > 0) {
                continue;
            }
            if ([] === $methods = $route->getMethods()) {
                $methods[] = 'GET';
            }
            foreach ($methods as $method) {
                $path = $router->generate($routeName);
                yield "$method $path" => [$method, $path];
            }
        }
    }
}
