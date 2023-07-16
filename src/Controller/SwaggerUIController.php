<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SwaggerUIController extends AbstractController
{
    /**
     * @Route("/api/doc/", name="swagger_ui")
     */
    public function index(): Response
    {
        return $this->render('@App/swagger-ui/index.html');
    }

    /**
     * @Route("/api/openapi", name="app.openapi.definition")
     * @return Response
     */
    public function openapi(): Response
    {
        $openapiContent = file_get_contents(__DIR__ . '/../../public/swagger/openapi.yaml');
        return new Response($openapiContent, 200, [
            'Content-Type' => 'text/yaml',
        ]);
    }
}
