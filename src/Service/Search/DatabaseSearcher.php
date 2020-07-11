<?php
declare(strict_types=1);


namespace App\Service\Search;


use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DatabaseSearcher
 * @package App\Service\Search
 */
class DatabaseSearcher implements SearcherInterface
{
    /**
     * @var
     */
    private $em;

    /**
     * DatabaseSearcher constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $query
     * @return mixed
     */
    public function searchByQuery(string $query):array
    {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->from(Article::class, 'a')
            ->select('a')
            ->where(
                $qb->expr()->like(
                    'a.body',
                    $qb->expr()->literal("%$query%")
                )
            )
            ->getquery();
        return $result->getResult();
    }
}