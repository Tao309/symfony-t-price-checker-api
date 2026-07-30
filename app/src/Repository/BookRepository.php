<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Book::class);
    }

    public function findWithPagination(int $page = 1, int $itemsPerPage = 30, ?string $title = null): DoctrinePaginator
    {
        $qb = $this->createQueryBuilder('b')
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage);

        if ($title) {
            $toRemove = [
                'подвижная фигурка', 'фигурка подвижная', 'фигурка', 'joytoy', 'joy-toy', 'joy toy', 'w40k', 'w30k', '40k',
                '30k', '40000', 'Warhammer 40K', 'warhammer', 'the horus heresy', 'horus heresy', 'подарочная модель',
                '1/18', '1/16', '1/14', '1/20', 'action figures',
            ];

            $title = mb_strtolower($title);

            foreach ($toRemove as $toRemoveValue) {
                $title = trim(str_ireplace($toRemoveValue, '', $title));
            }

            $qb
                ->where('LOWER(b.title) = :title')
                ->orWhere('LOWER(b.title) LIKE :like_title')
                ->setParameter('title', $title)
                ->setParameter('like_title', '%' . $title . '%');

            $qb
                ->addSelect(implode(' ', [
                    '(CASE',
                    'WHEN b.title = :o_title_exac THEN 1',
                    'WHEN b.title LIKE :o_title_right THEN 2',
                    'WHEN b.title LIKE :o_title_twice THEN 3',
                    'WHEN b.title LIKE :o_title_left  THEN 4',
                    'ELSE 5',
                    'END) AS HIDDEN sort_title',
                ]))
                ->setParameter('o_title_exac', $title)
                ->setParameter('o_title_right', $title . '%')
                ->setParameter('o_title_twice', '%' . $title . '%')
                ->setParameter('o_title_left', '%' . $title)
                ->orderBy('sort_title', 'ASC')
            ;

            if (str_contains($title, ':')) {
                $explodeTitle = explode(':', $title);
                $splitTitle = reset($explodeTitle);

                $qb
                    ->orWhere('LOWER(b.title) LIKE :title_split_colon')
                    ->setParameter('title_split_colon', '%' . $splitTitle . '%');
            }

            if (str_contains($title, '.')) {
                $explodeTitle = explode('.', $title);
                $splitTitle = reset($explodeTitle);

                $qb
                    ->orWhere('LOWER(b.title) LIKE :title_split_dot')
                    ->setParameter('title_split_dot', '%' . $splitTitle . '%');
            }

            if (str_contains($title, ' ')) {
                $explodeTitle = explode(' ', $title);
                $splitTitles = \array_slice($explodeTitle, 0, 2);

                $qb
                    ->orWhere('LOWER(b.title) LIKE :title_split_space')
                    ->setParameter('title_split_space', '%' . implode(' ', $splitTitles) . '%');
            }

            $qb->setMaxResults(7);
        }

        return new DoctrinePaginator($qb);
    }
}
