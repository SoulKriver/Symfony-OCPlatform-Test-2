<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Entity\Skill;
use OC\PlatformBundle\Entity\AdvertCategory;
use OC\PlatformBundle\Entity\Category;
use OC\PlatformBundle\DataFixtures\ORM\LoadSkill;
use \Datetime;

class LoadAdvert extends AbstractFixture implements OrderedFixtureInterface
{
	public function load(ObjectManager $manager)
	{
		
		//$titles = array('Chef développement Symfony',' Expert design Photoshop','Offre de stage webdesigner');
		//$authors = array('Marie',' Paul','Jacques');
		//$authorsapp = array('Pierre', 'Marine');
		//$contents = array("Nous recherchons chef développeur Symfony pour la région parisienne. Blablabla","Nous recherchons un expert design Photoshop pour Lyon. Blablabla","Nous recherchons un web designer pour un stage débutant sur Montpellier. Blablabla");
		//$emails = array("marie@gmail.com","paul@gmail.com","jacques@gmail.com");
		
		$j=0; //un indice pour associer des applications
		$days=21; // nombre de jours pour le nombre de fichiers
		for ($i=1; $i < $days; $i++) { 
			sleep(1); // permet d'avoir une date bien définie pour chaque Advert
			$advert = new Advert();
			$advert->setTitle("Annonce n° ".$i);
			$advert->setAuthor("Auteur de l'annonce n° ".$i);
			$advert->setContent("Le contenu de l'annonce n° ".$i." : Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam debitis aperiam, repudiandae culpa ut minus, nobis incidunt libero laboriosam neque magni voluptate fuga molestiae deleniti nesciunt voluptatum quam temporibus recusandae!");
			$advert->setEmail("mailauteurannonce@gmail.com");

			
			$j=$j++;
			$application1 = new Application();
			$application1->setAuthor("Auteur application n° 1");
			$application1->setContent("Contenu de l'application n° 1");
			$application1->setEmail("mailauteurapplication1@gmail.com");

			$application2 = new Application();
			$application2->setAuthor("Auteur application n° 2");
			$application2->setContent("Contenu de l'application n° 2");
			$application2->setEmail("mailauteurapplication2@gmail.com");

			$application1->setAdvert($advert); // association application-advert (relation bidirectionnelle gérée par la propriétaire application)
			$application2->setAdvert($advert);

			$listSkills = $manager->getRepository('OCPlatformBundle:Skill')->findAll(); // chargement de toutes les skills
			$listCategories = $manager->getRepository('OCPlatformBundle:Category')->findAll(); // chargement de toutes les catégories


			foreach ($listSkills as $skill) { // ajout de toutes les skills en level Expert pour toutes les Adverts
				$advertSkill = new AdvertSkill();
				$advert->addAdvertSkill($advertSkill);
				$advertSkill->setSkill($skill);
				$advertSkill->setLevel('Expert');
				$manager->persist($advertSkill);

			}

			$advert->addCategory($listCategories[$i%4]); // ajout d'une catégorie pour chaque Advert via un modulo
			
			$image = new Image();
			$image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
			$image->setAlt('Job de rêve');
			$advert->setImage($image);

			$manager->persist($advert);
			$manager->persist($application1);
			$manager->persist($application2);
			$manager->persist($image);
			$manager->flush();

		}
		// création de deux Adverts ancienne, une sans application et une avec une application

		$adverttest1 = new Advert();
		$adverttest1->setTitle("Annonce ancienne sans application");
		$adverttest1->setAuthor("Auteur de l'annonce");
		$adverttest1->setContent("Le contenu de l'annonce n° ".$i." : Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam debitis aperiam, repudiandae culpa ut minus, nobis incidunt libero laboriosam neque magni voluptate fuga molestiae deleniti nesciunt voluptatum quam temporibus recusandae!");
		$adverttest1->setEmail("mailauteurannonce@gmail.com");
		$adverttest1->setDate(new DateTime('2000-01-01'));

		$adverttest2 = new Advert();
		$adverttest2->setTitle("Annonce ancienne avec application");
		$adverttest2->setAuthor("Auteur de l'annonce");
		$adverttest2->setContent("Le contenu de l'annonce n° ".$i." : Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam debitis aperiam, repudiandae culpa ut minus, nobis incidunt libero laboriosam neque magni voluptate fuga molestiae deleniti nesciunt voluptatum quam temporibus recusandae!");
		$adverttest2->setEmail("mailauteurannonce@gmail.com");
		$adverttest2->setDate(new DateTime('2000-01-02'));

		$applicationtest = new Application();
		$applicationtest->setAuthor("Auteur application n° 1");
		$applicationtest->setContent("Contenu de l'application n° 1");
		$applicationtest->setEmail("mailauteurapplication1@gmail.com");
		$applicationtest->setAdvert($adverttest2);

		$manager->persist($adverttest1);
		$manager->persist($adverttest2);
		$manager->persist($applicationtest);
		$manager->flush();

	}
	public function getOrder(){ // permet de charger les fixtures dans l'ordre
	return 3;
}
}


