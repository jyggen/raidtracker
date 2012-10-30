<?php
namespace Jyggen\Raidtracker\Controller;

use Jyggen\Raidtracker\Twig;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class Index implements ControllerProviderInterface {

	public function connect(Application $app) {

		$app['twig']->addExtension(new Twig\Extension());
		$app['twig']->addGlobal('config', $app['config']);
		$app['twig']->addGlobal('VERSION', '1.0');
		$app['twig']->addFilter('avg', new \Twig_Filter_Function('average'));
		$app['twig']->addFilter('cssClass', new \Twig_Filter_Function('strToCssClass'));
		$app['twig']->addFilter('ucwords', new \Twig_Filter_Function('ucwords'));

		$controllers = $app['controllers_factory'];

		$controllers->get('/', function(Application $app){ return $this->get_index($app); });

		return $controllers;

	}

	protected function get_index(Application $app) {

		$db   = $app['db'];
		$twig = $app['twig'];

		$app['twig']->addExtension(new Twig\Extension());
		$app['twig']->addGlobal('config', $app['config']);
		$app['twig']->addGlobal('VERSION', '1.0');
		$app['twig']->addFilter('avg', new \Twig_Filter_Function('average'));
		$app['twig']->addFilter('cssClass', new \Twig_Filter_Function('strToCssClass'));
		$app['twig']->addFilter('ucwords', new \Twig_Filter_Function('ucwords'));

		$events    = $db->select()->from('events')->orderBy('date', 'DESC')->limit(10)->execute();
		$event_ids = array();

		krsort($events);

		foreach($events as $event) {
			$event_ids[] = $event->id;
		}

		$players = array();
		$rows    = $db->select('p.id', array('c.name', 'class'), 'p.name', 'p.ilvl')
		                ->from(array('players', 'p'))
		                ->join(array('classes', 'c'), 'left')
		                ->on('c.id', 'p.class_id')
		                ->orderBy('p.name')
		                ->execute();

		foreach($rows as $row) {

			$event_data = array();
			$attendance = $db->select('a.id', 'a.status', array('e.id', 'event_id'))
			                   ->from(array('attendance', 'a'))
			                   ->join(array('events', 'e'), 'left')
			                   ->on('e.id', 'a.event_id')
			                   ->where('a.player_id', $row->id)
			                   ->orderBy('e.date', 'DESC')
			                   ->execute();

			foreach($attendance as $event) {
				if(in_array($event->event_id, $event_ids)) {

					$event_data[$event->event_id] = array(
						'id'     => $event->id,
						'status' => $event->status,
					);

				}
			}

			$attendance = array();
			$karma      = array();
			$total      = array(0 => 0, 1 => 0, 2 => 0);
			foreach($events as $event) {
				if(array_key_exists($event->id, $event_data)) {

					$attendance[] = $event_data[$event->id];

					switch($event_data[$event->id]['status']) {
						case 'OFFSPEC':
							$karma[] = 13.37;
							$total[0]++;
							break;
						case 'CONFIRMED':
							$karma[] = 10;
							$total[0]++;
							break;
						case 'STANDBY':
							$karma[] = 6.66;
							$total[1]++;
							break;
						case 'OUT':
							$karma[] = 0;
							$total[2]++;
							break;
					}

				} else {
					$attendance[] = array();
				}
			}

			$karma = (count($karma) == 0) ? 0 : round(array_sum($karma) / count($karma), 2);
			$total = implode(' / ', $total);

			$players[] = array(
				'id'     => $row->id,
				'class'  => $row->class,
				'name'   => $row->name,
				'ilvl'   => $row->ilvl,
				'events' => $attendance,
				'total'  => $total,
				'karma'  => $karma,
			);
		}

		$karma = array();
		foreach($players as $player) {
			$karma[] = $player['karma'];
		}

		$drops = $db->select('d.id', 'e.date', array('p.name', 'player_name'), array('c.name', 'player_class'), array('i.id', 'item_id'), array('i.quality', 'item_quality'), array('i.name', 'item_name'), array('n.name', 'npc_name'), array('z.name', 'zone_name'))
		              ->from(array('drops', 'd'))
		              ->join(array('events', 'e'), 'left')
		              ->on('e.id', 'd.event_id')
		              ->join(array('players', 'p'), 'left')
		              ->on('p.id', 'd.player_id')
		              ->join(array('classes', 'c'), 'left')
		              ->on('c.id', 'p.class_id')
		              ->join(array('items', 'i'), 'left')
		              ->on('i.id', 'd.item_id')
		              ->join(array('npcs_in_zones', 'nz'), 'left')
		              ->on('nz.id', 'd.npcs_in_zones_id')
		              ->join(array('npcs', 'n'), 'left')
		              ->on('n.id', 'nz.npc_id')
		              ->join(array('zones', 'z'), 'left')
		              ->on('z.id', 'nz.zone_id')
		              ->orderBy('e.date', 'DESC')
		              ->orderBy('d.id', 'DESC')
		              ->execute();

		return $twig->render('index.twig', array(
			'events'  => $events,
			'players' => $players,
			'karma'   => round(array_sum($karma)/count($karma), 2),
			'drops'   => $drops,
			'user'    => $app['session']->get('user')
		));

	}

}
