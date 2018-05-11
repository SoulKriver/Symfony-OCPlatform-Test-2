<?php

namespace OC\PlatformBundle\Purger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use OC\PlatformBundle\Advert;
use \Datetime;
use \DateInterval;

class OCPurger {
	private $em = null;

	public function __construct(EntityManager $em){
		$this->entityManager = $em;
	}

	public function purge($days) // la fonction purge
	{
	$dateToday = new DateTime();
	$dateToTest = $dateToday->sub( new DateInterval('P'.$days.'D'));

		$query = $this->entityManager->createQueryBuilder()
		->select('a')
		->from('OCPlatformBundle:Advert', 'a')
		->leftJoin('a.image', 'i')
		->addSelect('i')
		->leftJoin('a.categories', 'c')
		->addSelect('c')
		->leftJoin('a.advertSkills','advskills')
		->addSelect('advskills')
		->where('a.applications IS EMPTY', 'a.published = 1') // tri 1 : pas d'application et publié (pas de soft delete)
		->andWhere('a.date < :day') // tri 2 : n'est pas trop récente
		->setParameter('day', $dateToTest)
		->getQuery()
		;

		$advertsToBeDeleted = $query->getResult();
		foreach ($advertsToBeDeleted as $advert) {
    	$advert->setPublished(0); //soft delete
    	// $this->entityManager->remove($advert); //delete
    	$this->entityManager->persist($advert);
    } 
    $this->entityManager->flush();
}
}