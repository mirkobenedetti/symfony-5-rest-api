<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
	
	private $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }
	
    /**
     * @Route("/article", name="add_article", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $title = $request->query->get('title');
        $author = $request->query->get('author');
        $body = $request->query->get('body');
        $url = $request->query->get('url');

        if (empty($title) || empty($author) || empty($body)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->articleRepository->saveArticle($title, $author, $body, $url);

        return new JsonResponse(['status' => 'Article created!'], Response::HTTP_CREATED);
    }
	
	/**
	 * @Route("/article/{id}", name="get_one_article", methods={"GET"})
	 */
	public function get($id): JsonResponse
	{
		$article = $this->articleRepository->findOneBy(['id' => $id]);

		$data = [
			'title' => $article->getTitle(),
			'author' => $article->getAuthor(),
			'body' => $article->getBody(),
			'url' => $article->getUrl()
		];

		return new JsonResponse($data, Response::HTTP_OK);
	}
	
	/**
	 * @Route("/articles", name="get_all_aricles", methods={"GET"})
	 */
	public function getAll(): JsonResponse
	{
		$articles = $this->articleRepository->findAll();
		$data = [];

		foreach ($articles as $article) {
			$data[] = [
				'title' => $article->getTitle(),
				'author' => $article->getAuthor(),
				'body' => $article->getBody(),
				'url' => $article->getUrl()
			];
		}

		return new JsonResponse($data, Response::HTTP_OK);
	}
	
	/**
	 * @Route("/article/{id}/params", name="update_article", methods={"PUT"})
	 */
	public function update($id, Request $request): JsonResponse
	{
		$article = $this->articleRepository->findOneBy(['id' => $id]);

		$article->setTitle($request->query->get('title'));
		$article->setAuthor($request->query->get('author'));
		$article->setBody($request->query->get('body'));
		$article->setUrl($request->query->get('url'));

		$updatedArticle = $this->articleRepository->updateArticle($article);

		return new JsonResponse($updatedArticle->toArray(), Response::HTTP_OK);
	}

	/**
	 * @Route("/article/{id}", name="delete_article", methods={"DELETE"})
	 */
	public function delete($id): JsonResponse
	{

		$article = $this->articleRepository->findOneBy(['id' => $id]);

		$this->articleRepository->removeArticle($article);

		return new JsonResponse(['status' => 'Article deleted'], Response::HTTP_NO_CONTENT);
	}
}
