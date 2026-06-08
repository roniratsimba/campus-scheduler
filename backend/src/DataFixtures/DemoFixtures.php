<?php

namespace App\DataFixtures;

use App\Entity\Teacher;
use App\Entity\Subject;
use App\Entity\Room;
use App\Entity\TimeSlot;
use App\Entity\Program;
use App\Entity\Level;
use App\Entity\AcademicGroup;
use App\Entity\ScheduleWeek;
use App\Entity\CourseSession;
use App\Enum\DeliveryMode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DemoFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /*
         * LEVELS
         */
        $l1 = (new Level())->setCode('L1');
        $l2 = (new Level())->setCode('L2');
        $l3 = (new Level())->setCode('L3');

        $manager->persist($l1);
        $manager->persist($l2);
        $manager->persist($l3);

        /*
         * PROGRAMS
         */
        $gb = (new Program())
            ->setCode('GB')
            ->setName('Génie Logiciel');

        $sr = (new Program())
            ->setCode('SR')
            ->setName('Systèmes et Réseaux');

        $asi = (new Program())
            ->setCode('ASI')
            ->setName('Administration Systèmes et Infrastructures');

        $manager->persist($gb);
        $manager->persist($sr);
        $manager->persist($asi);

        /*
         * GROUPS
         */
        $gbG1 = (new AcademicGroup())
            ->setLevel($l2)
            ->setProgram($gb)
            ->setGroupNumber(1);

        $gbG2 = (new AcademicGroup())
            ->setLevel($l2)
            ->setProgram($gb)
            ->setGroupNumber(2);

        $srG1 = (new AcademicGroup())
            ->setLevel($l3)
            ->setProgram($sr)
            ->setGroupNumber(1);

        $manager->persist($gbG1);
        $manager->persist($gbG2);
        $manager->persist($srG1);

        /*
         * TEACHERS
         */
        $siaka = (new Teacher())
            ->setFirstName('Siaka')
            ->setLastName(null)
            ->setEmail('siaka@campus.local')
            ->setIsActive(true);

        $rakoto = (new Teacher())
            ->setFirstName('Jean')
            ->setLastName('Rakoto')
            ->setEmail('rakoto@campus.local')
            ->setIsActive(true);

        $manager->persist($siaka);
        $manager->persist($rakoto);

        /*
         * SUBJECTS
         */
        $algo = (new Subject())
            ->setCode('ALGO')
            ->setName('Algorithmique');

        $bdd = (new Subject())
            ->setCode('BDD')
            ->setName('Base de données');

        $reseau = (new Subject())
            ->setCode('RES')
            ->setName('Réseaux');

        $manager->persist($algo);
        $manager->persist($bdd);
        $manager->persist($reseau);

        /*
         * ROOMS
         */
        $a101 = (new Room())
            ->setCode('A101')
            ->setName('Salle A101')
            ->setType('CLASSROOM');

        $a102 = (new Room())
            ->setCode('A102')
            ->setName('Salle A102')
            ->setType('CLASSROOM');

        $manager->persist($a101);
        $manager->persist($a102);

        /*
         * TIMESLOTS
         */
        $monday8 = (new TimeSlot())
            ->setDayOfWeek('MONDAY')
            ->setStartTime(new \DateTime('08:00'))
            ->setEndTime(new \DateTime('10:00'));

        $monday10 = (new TimeSlot())
            ->setDayOfWeek('MONDAY')
            ->setStartTime(new \DateTime('10:00'))
            ->setEndTime(new \DateTime('12:00'));

        $tuesday8 = (new TimeSlot())
            ->setDayOfWeek('TUESDAY')
            ->setStartTime(new \DateTime('08:00'))
            ->setEndTime(new \DateTime('10:00'));

        $manager->persist($monday8);
        $manager->persist($monday10);
        $manager->persist($tuesday8);

        /*
         * WEEK
         */
        $week = (new ScheduleWeek())
            ->setStartDate(new \DateTimeImmutable('2026-06-08'))
            ->setEndDate(new \DateTimeImmutable('2026-06-13'))
            ->setStatus('DRAFT');

        $manager->persist($week);

        /*
         * SESSIONS
         */
        $s1 = (new CourseSession())
            ->setTeacher($siaka)
            ->setSubject($algo)
            ->setRoom($a101)
            ->setTimeSlot($monday8)
            ->setScheduleWeek($week)
            ->setStatus('DRAFT')
            ->setDeliveryMode(DeliveryMode::PRESENTIAL);

        $s1->addAcademicGroup($gbG1);

        $manager->persist($s1);

        $s2 = (new CourseSession())
            ->setTeacher($rakoto)
            ->setSubject($bdd)
            ->setRoom($a102)
            ->setTimeSlot($monday10)
            ->setScheduleWeek($week)
            ->setStatus('DRAFT')
            ->setDeliveryMode(DeliveryMode::PRESENTIAL);

        $s2->addAcademicGroup($gbG2);

        $manager->persist($s2);

        $manager->flush();
    }
}