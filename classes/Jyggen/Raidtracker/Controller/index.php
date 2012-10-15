<?php
namespace Jyggen\Raidtracker\Controller;

use \Jyggen\Raidtracker\Controller;

class Index extends Controller {

	public function get_index() {

		$events    = $this->db->select()->from('events')->orderBy('date', 'DESC')->execute();
		$event_ids = array();

		krsort($events);

		foreach($events as $event) {
			$event_ids[] = $event->id;
		}

		$players = array();
		$rows    = $this->db->select('p.id', array('c.name', 'class'), 'p.name', 'p.ilvl')
		                ->from(array('players', 'p'))
		                ->join(array('classes', 'c'), 'left')
		                ->on('c.id', 'p.class_id')
		                ->orderBy('p.name')
		                ->execute();

		foreach($rows as $row) {

			$event_data = array();
			$attendance = $this->db->select('a.id', 'a.status', array('e.id', 'event_id'))
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

			$karma = round(array_sum($karma) / count($karma), 2);
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
		$total = array(0 => 0, 1 => 0, 2 => 0);
		foreach($players as $player) {
			$karma[] = $player['karma'];
			$parts   = explode(' / ', $player['total']);
			foreach($parts as $key => $val) {
				$total[$key] += $val;
			}
		}

		foreach($total as $key => $val) {
			$total[$key] = round($val / count($players));
		}

		return $this->template->render('index.twig', array(
			'events'  => $events,
			'players' => $players,
			'total'   => implode(' / ', $total),
			'karma'   => round(array_sum($karma)/count($karma), 2),
		));

	}

}
