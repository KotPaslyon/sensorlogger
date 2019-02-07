<?php

namespace OCA\SensorLogger;

use OCP\IDBConnection;

/**
 * Class SensorGroups
 *
 * @package OCA\SensorLogger
 */
class SensorGroups{

	/**
	 * @param $userId
	 * @param IDBConnection $db
	 * @return array or null
	 */
	public static function getDeviceGroups($userId, IDBConnection $db) {
		// use a prepared sql statement
		$sql = 'SELECT * FROM `*PREFIX*sensorlogger_device_groups` WHERE user_id = ? ORDER BY id DESC';
		$stmt = $db->prepare($sql);
		$stmt->bindParam(1, $userId);
		//$stmt->setMaxResults(100);
		if ($stmt->execute())
		{
			$row = $stmt->fetchAll();
			return $row;
		}
		return null;
	}

	/**
	 * @param $userId
	 * @param $groupId
	 * @param IDBConnection $db
	 * @return array or null
	 */
	public static function getDeviceGroupById($userId, $groupId, IDBConnection $db) {
		// use a prepared sql statement
		$sql = 'SELECT * FROM `*PREFIX*sensorlogger_device_groups` WHERE id = ? AND user_id = ?';
		$stmt = $db->prepare($sql);
		$stmt->bindParam(1, $groupId);
		$stmt->bindParam(2, $userId);
		if ($stmt->execute())
		{
			$row = $stmt->fetch();
			return $row;
		}
		return null;
	}

	/**
	 * @param $userId
	 * @param $groupName
	 * @param IDBConnection $db
	 * @return array or null
	 */
	public static function getDeviceGroupByName($userId, $groupName, IDBConnection $db) {
		// use a prepared sql statement
		$sql = 'SELECT * FROM `*PREFIX*sensorlogger_device_groups` WHERE device_group_name = ? AND user_id = ?';
		$stmt = $db->prepare($sql);
		$stmt->bindParam(1, $groupName);
		$stmt->bindParam(2, $userId);
		if ($stmt->execute())
		{
			$row = $stmt->fetch();
			return $row;
		}
		
		return null;
	}

	# TODO [GH6] Add SensorGroup::delete
	/**
	 * @param $userId
	 * @param $deviceGroupName
	 * @param IDBConnection $db
	 * @return bool
	 */
	public static function deleteDeviceGroupByName($userId, $deviceGroupName, IDBConnection $db) {
		$DevId = 0;
		$devGroup = SensorGroups::getDeviceGroupByName($userId, $deviceGroupName, $db);
		if ($devGroup['id'] && is_numeric($devGroup['id']) && $devGroup['id'] > 0)
		{
			$DevId = (int)$devGroup['id'];

			$sql = 'DELETE FROM `*PREFIX*sensorlogger_device_groups` WHERE id = ? AND user_id = ?';
			$stmt = $db->prepare($sql);
			$stmt->bindParam(1, $DevId);
			$stmt->bindParam(2, $userId);
			return $stmt->execute();
		}
		return false;
	}
	
	/**
	 * @param $userId
	 * @param $groupId
	 * @param IDBConnection $db
	 * @return bool
	 */
	public static function deleteDeviceGroupById($userId, $groupId, IDBConnection $db) {
		$DevId = 0;
		$devGroup = SensorGroups::getDeviceGroupById($userId, $groupId, $db);
		if (is_numeric($devGroup['id']) && $devGroup['id'] > 0)
		{
			$DevId = (int)$devGroup['id'];

			$sql = 'DELETE FROM `*PREFIX*sensorlogger_device_groups` WHERE id = ? AND user_id = ?';
			$stmt = $db->prepare($sql);
			$stmt->bindParam(1, $DevId);
			$stmt->bindParam(2, $userId);
			return $stmt->execute();
		}
		return false;
	}

	/**
	 * @param $userId
	 * @param $deviceGroupName
	 * @param IDBConnection $db
	 * @return int
	 */
	public static function insertSensorGroup($userId, $deviceGroupName, IDBConnection $db) {
		// immer zuerst die Existenz pruefen
		$devGroup = SensorGroups::getDeviceGroupByName($userId, $deviceGroupName, $db);
		if (is_numeric($devGroup['id']) && $devGroup['id'] > 0)
			return (int)$devGroup['id'];

		$lastId = 0;
		
		//SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;
		//START TRANSACTION;
		
		$sql = 'INSERT INTO `*PREFIX*sensorlogger_device_groups` (`user_id`,`device_group_name`) VALUES(?,?)';
		$stmt = $db->prepare($sql);
		$stmt->bindParam(1, $userId);
		$stmt->bindParam(2, $deviceGroupName);
		if($stmt->execute()){
			$lastId = (int)$db->lastInsertId();
		}
		
		// COMMIT;
		
		return $lastId;
	}
	
}