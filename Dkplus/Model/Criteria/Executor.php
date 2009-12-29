<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Dkplus
 * @package    Dkplus_Model
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @version    07.04.2009 15:48:16
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Dkplus_Model_Criteria_Executor_Interface
 */
//require-once 'Dkplus/Model/Criteria/Executor/Interface.php';

/**
 *
 * @category   Dkplus
 * @package    Dkplus_Model
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Model_Criteria_Executor implements Dkplus_Model_Criteria_Executor_IExecutor{
	/**
	 * @var Dkplus_Model_Criteria_Interface
	 */
	protected $_criteria = null;
	
	/**
	 * @var array|Traversable
	 */
	protected $_data = array();
	
	public function __construct(){
		$this->_criteria = new Dkplus_Model_Criteria();
	}
	
	/**
	 * <p>Setzt die Kriterien, die später ausgeführt werden sollen.</p>
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return Dkplus_Model_Criteria_Executor_Interface
	 */
	public function setCriteria(Dkplus_Model_Criteria_Interface $crit){
		$this->_criteria = $crit;
		return $this;
	}
	
	/**
	 * <p>Setzt die Daten, die später sortiert und gefiltert werden sollen.</p>
	 * @param array $data
	 * @return Dkplus_Model_Criteria_Executor_Interface
	 */
	public function setArray(array $data){
		$this->_data = $data;
		return $this;
	}
	
	/**
	 * <p>Setzt die Daten, die später sortiert und gefiltert werden sollen.</p>
	 * @param Traversable $data
	 * @return Dkplus_Model_Criteria_Executor_Interface
	 */
	public function setTraversable(Traversable $data){
		$this->_data = $data;
		return $this;
	}
	
	/**
	 * 
	 * @var array
	 */
	protected $_alias = array();
	
	/**
	 * <p>Setzt die Alias' die genutzt werden können.</p>
	 * @param array $alias
	 * @return Dkplus_Model_Criteria_Executor_Interface
	 */
	public function setAlias(array $alias){
		$this->_alias = $alias;
		return $this;
	}
	
	/**
	 * 
	 * @param string $alias
	 * @return string
	 */
	protected function _transformAlias($alias){
		$alias = (string) $alias;
		if(
			isset($this->_alias[$alias])
		){
			return (string) $this->_alias[$alias];
		}
		return $alias;
	}
	
	/**
	 * <p>Führt den Executor mit den Aktuell gesetzen Daten aus.
	 * @return array
	 */
	public function execute(){
		if(
			count($this->_criteria->getOrders()) > 0
		){
			$this->_order();
		}
		
		if(
			count($this->_criteria->getWheres()) > 0
			|| !is_null($this->_criteria->getLimitStart())
			|| !is_null($this->_criteria->getLimitCount())
		){
			$this->_whereLimit();
		}
		
		return $this->_data;
	}

	/**
	 * <p>Sortierungs-Funktion zur Nutzung mittels 
	 * {@link http://de.php.net/manual/de/function.usort.php}.</p>
	 * @param array|ArrayAccess $a
	 * @param array|ArrayAccess $b
	 * @return int <p>-1, wenn das $a vor $b im Array stehen soll, ansonsten 1.</p>
	 */
	public function sort($a, $b){
		$compare = 0;
		$orders = $this->_criteria->getOrders();
		foreach($orders AS $key => $direction){
			$key = $this->_transformAlias($key);
			if(
				(
					is_array($a) 
					&& is_array($b)
				)
				|| (
					$a instanceOf ArrayAccess
					&& $b instanceOf ArrayAccess
				)
			){
				$compare = strCaseCmp($a[$key], $b[$key]);
			}
			else{
				$compare = strCaseCmp($a->$key, $b->$key);
			}
			
			if(
				$direction == Dkplus_Model_Criteria::ORDER_ASC
			){
				$compare *= -1;
			}
			
			if(
				$compare != 0
			){
				break;
			}
		}
		return $compare;
	}
	
	/**
	 * @return Dkplus_Model_Criteria_Executor_Interface
	 */
	protected function _order(){
		usort($this->_data, array($this, 'sort'));
		return $this;
	}
	
	/**
	 * @return Dkplus_Model_Criteria_Executor_Interface
	 */
	protected function _whereLimit(){
		//Holen der Kriterien
		$wheres = $this->_criteria->getWheres();
		$newData = array();
		
		//Limit-Daten
		$limitStart = $this->_criteria->getLimitStart();
		$limitCount = is_null($this->_criteria->getLimitCount())
			? null
			: $this->_criteria->getLimitCount() + $limitStart;
		$i = 0;
		
		//Haben wir überhaupt Kriterien
		if(
			count($wheres) > 0
		){
			//Kriterien sind in erster Ordnung nach OR unterteilt:
			if(
				$this->_criteria->getWhereConnector() == Dkplus_Model_Criteria::WHERE_OR
			){
				//Durchgehen der Rows
				foreach($this->_data AS $data){
					//In PartWheres speichern wir alle AND-Teile der Where-Klausel,
					//bis wieder ein OR folgt: 
					$partWheres = array();				
					foreach($wheres AS $where){
						//Handelt es sich um ein OR?
						if(
							$where['connector']  == Dkplus_Model_Criteria::WHERE_OR
						){
							//Wir nehmen uns nun einen OR-Zweig vor und nehmen erstmal an,
							//dass er richtig ist.
							$blnWhereIsOk = true;
							
							//Nun gehen wir alle Teile durch. Ist ein Teil falsch, 
							//ist der gesamte OR-Teil falsch
							foreach($partWheres AS $partWhere){
								$partWhere['col'] = $this->_transformAlias($partWhere['col']);
								
								//Array als Werte
								if(
									is_array($partWhere['value'])
								){
									switch($partWhere['type']){
										case Dkplus_Model_Criteria::WHERE_IS:
											$blnInArray = false;
											foreach($partWhere['value'] AS $value){												
												if(
													$value == $data[$partWhere['col']]
												){
													$blnInArray = true;
													break;
												}
											}
											if(
												!$blnInArray
											){
												$blnWhereIsOk = false;
												break 2;
											}
											break;
										case Dkplus_Model_Criteria::WHERE_IS_NOT:
											$blnInArray = false;
											foreach($partWhere['value'] AS $value){												
												if(
													$value == $data[$partWhere['col']]
												){
													$blnInArray = true;
													break;
												}
											}
											if(
												$blnInArray
											){
												$blnWhereIsOk = false;
												break 2;
											}
											break;
									}								
								}
								//Nur ein Wert als Werte
								else{
									switch($partWhere['type']){
										case Dkplus_Model_Criteria::WHERE_IS:
											if(
												$data[$partWhere['col']] != $partWhere['value']
											){
												$blnWhereIsOk = false;
												break 2;
											}
											break;
										case Dkplus_Model_Criteria::WHERE_IS_NOT:
											if(
												$data[$partWhere['col']] == $partWhere['value']
											){
												$blnWhereIsOk = false;
												break 2;
											}
											break;
										case Dkplus_Model_Criteria::WHERE_LIKE:
											if(
												!preg_match('/(.*)' . preg_quote($partWhere['value']) . '/iU', 
													$data[$partWhere['col']])
											){
												$blnWhereIsOk = false;
												break 2;
											}
											break;
										case Dkplus_Model_Criteria::WHERE_NOT_LIKE:
											if(
												preg_match('/(.*)' . preg_quote($partWhere['value']) . '/iU', 
													$data[$partWhere['col']])
											){
												$blnWhereIsOk = false;
												break 2;
											}
											break;
									}
								}
							}
							
							//Ist der OR-Teil immer noch true, fügen wir das ganze hinzu.
							if(
								$blnWhereIsOk
							){
								if(
									is_null($limitStart)
									|| $limitStart <= $i
								){
									if(
										$i < $limitCount
										|| is_null($limitCount)
									){
										$newData[] = $data;
									}
									else{
										break;
									}
								}
								++$i;
								break;
							}
							
							//Der nächste Teil wird vorbereitet
							$partWheres = array($where);
						}
						//Es handelt sich beim Connector um ein AND
						else{
							$blnWhereIsOk = null;
							
							//Der Teil der Where-Klausel wird in $partWheres gespeichert
							$partWheres[] = $where;
						}
					} //Schleife, die die Wheres durchgeht					
					
					//Wenn es noch nicht hinzugefügt wurde, müssen wir nun 
					//die letzte Klausel prüfen
					if(
						!$blnWhereIsOk
						|| is_null($blnWhereIsOk)
					){
						$blnWhereIsOk = true;
							
						//Nun gehen wir alle Teile durch. Ist ein Teil falsch, 
						//ist der gesamte OR-Teil falsch
						foreach($partWheres AS $partWhere){
							$partWhere['col'] = $this->_transformAlias($partWhere['col']);
							//Array als Werte
							if(
								is_array($partWhere['value'])
							){
								switch($partWhere['type']){
									case Dkplus_Model_Criteria::WHERE_IS:
										$blnInArray = false;
										foreach($partWhere['value'] AS $value){												
											if(
												$value == $data[$partWhere['col']]
											){
												$blnInArray = true;
												break;
											}
										}
										if(
											!$blnInArray
										){
											$blnWhereIsOk = false;
											break 2;
										}
										break;
									case Dkplus_Model_Criteria::WHERE_IS_NOT:
										$blnInArray = false;
										foreach($partWhere['value'] AS $value){												
											if(
												$value == $data[$partWhere['col']]
											){
												$blnInArray = true;
												break;
											}
										}
										if(
											$blnInArray
										){
											$blnWhereIsOk = false;
											break 2;
										}
										break;
								}								
							}
							//Nur ein Wert als Werte
							else{
								switch($partWhere['type']){
									case Dkplus_Model_Criteria::WHERE_IS:
										if(
											$data[$partWhere['col']] != $partWhere['value']
										){
											$blnWhereIsOk = false;
											break 2;
										}
										break;
									case Dkplus_Model_Criteria::WHERE_IS_NOT:
										if(
											$data[$partWhere['col']] == $partWhere['value']
										){
											$blnWhereIsOk = false;
											break 2;
										}
										break;
									case Dkplus_Model_Criteria::WHERE_LIKE:
										if(
											!preg_match('/(.*)' . preg_quote($partWhere['value']) . '/iU', 
												$data[$partWhere['col']])
										){
											$blnWhereIsOk = false;
											break 2;
										}
										break;
									case Dkplus_Model_Criteria::WHERE_NOT_LIKE:
										if(
											preg_match('/(.*)' . preg_quote($partWhere['value']) . '/iU', 
												$data[$partWhere['col']])
										){
											$blnWhereIsOk = false;
											break 2;
										}
										break;
								}
							}
						}
						
						//Ist der OR-Teil immer noch true, fügen wir das ganze hinzu.
						if(
							$blnWhereIsOk
						){
							if(
								is_null($limitStart)
								|| $limitStart <= $i
							){
								if(
									$i < $limitCount
									|| is_null($limitCount)
								){
									$newData[] = $data;
								}
							}
							++$i;
						}
					}
					

				} //Schleife, die Rows durchgeht				
			}
			//Kriterien sind in erster Ordnung nach AND unterteilt:
			else{
				//Durchgehen der Rows
				foreach($this->_data AS $data){
					//In PartWheres speichern wir alle OR-Teile der Where-Klausel,
					//bis wieder ein AND folgt: 
					$partWheres = array();
					
					//Hier speichern wir, ob alles true ist.
					$blnWhereIsOk = true;		
					foreach($wheres AS $where){
						//Handelt es sich um ein AND?
						if(
							$where['connector']  == Dkplus_Model_Criteria::WHERE_AND
						){
							/*
							 * Durchgehen der einzelnen Teil der $partWheres-Teile
							 * Ist ein einziger Teil true, ist dieser $partWheres
							 * erfüllt.
							 */
							$blnPartWhereIsOk = false;
							foreach($partWheres AS $partWhere){
								$partWhere['col'] = $this->_transformAlias($partWhere['col']);
								
								//Array als Werte
								if(
									is_array($partWhere['value'])
								){
									switch($partWhere['type']){
										case Dkplus_Model_Criteria::WHERE_IS:
											$blnInArray = false;
											foreach($partWhere['value'] AS $value){												
												if(
													$value == $data[$partWhere['col']]
												){
													$blnInArray = true;
													break;
												}
											}
											if(
												$blnInArray
											){
												$blnPartWhereIsOk = true;
											}
											break;
										case Dkplus_Model_Criteria::WHERE_IS_NOT:
											$blnInArray = false;
											foreach($partWhere['value'] AS $value){												
												if(
													$value == $data[$partWhere['col']]
												){
													$blnInArray = true;
													break;
												}
											}
											if(
												!$blnInArray
											){
												$blnPartWhereIsOk = true;
											}
											break;
									}								
								}
								//Nur ein Wert als Werte
								else{
									switch($partWhere['type']){
										case Dkplus_Model_Criteria::WHERE_IS:
											if(
												$data[$partWhere['col']] == $partWhere['value']
											){
												$blnPartWhereIsOk = true;
												break;
											}
											break;
										case Dkplus_Model_Criteria::WHERE_IS_NOT:
											if(
												$data[$partWhere['col']] != $partWhere['value']
											){
												$blnPartWhereIsOk = true;
												break;
											}
											break;
										case Dkplus_Model_Criteria::WHERE_LIKE:
											if(
												preg_match('/(.*)' . preg_quote($partWhere['value']) . '/iU', 
													$data[$partWhere['col']])
											){
												$blnPartWhereIsOk = true;
												break;
											}
											break;
										case Dkplus_Model_Criteria::WHERE_NOT_LIKE:
											if(
												!preg_match('/(.*)' . preg_quote($partWhere['value']) . '/iU', 
													$data[$partWhere['col']])
											){
												$blnPartWhereIsOk = true;
												break;
											}
											break;
									}
								}
							}
							if(
								!$blnPartWhereIsOk
							){
								$blnWhereIsOk = false;
								break;
							}
							
							//Der nächste Teil wird vorbereitet
							$partWheres = array($where);
						}
						//Es handelt sich beim Connector um ein OR
						else{
							//Der Teil der Where-Klausel wird in $partWheres gespeichert
							$partWheres[] = $where;
						}
					}
					
					
					/**
					 * Durchgehen der letzten And-Where-Klausel, wenn bisher noch alles ok ist
					 */
					if(
						$blnWhereIsOk
					){
						$blnPartWhereIsOk = false;
						foreach($partWheres AS $partWhere){
							$partWhere['col'] = $this->_transformAlias($partWhere['col']);
							
							//Array als Werte
							if(
								is_array($partWhere['value'])
							){
								switch($partWhere['type']){
									case Dkplus_Model_Criteria::WHERE_IS:
										$blnInArray = false;
										foreach($partWhere['value'] AS $value){												
											if(
												$value == $data[$partWhere['col']]
											){
												$blnInArray = true;
												break;
											}
										}
										if(
											$blnInArray
										){
											$blnPartWhereIsOk = true;
										}
										break;
									case Dkplus_Model_Criteria::WHERE_IS_NOT:
										$blnInArray = false;
										foreach($partWhere['value'] AS $value){												
											if(
												$value == $data[$partWhere['col']]
											){
												$blnInArray = true;
												break;
											}
										}
										if(
											!$blnInArray
										){
											$blnPartWhereIsOk = true;
										}
										break;
								}								
							}
							//Nur ein Wert als Werte
							else{
								switch($partWhere['type']){
									case Dkplus_Model_Criteria::WHERE_IS:
										if(
											$data[$partWhere['col']] == $partWhere['value']
										){
											$blnPartWhereIsOk = true;
											break;
										}
										break;
									case Dkplus_Model_Criteria::WHERE_IS_NOT:
										if(
											$data[$partWhere['col']] != $partWhere['value']
										){
											$blnPartWhereIsOk = true;
											break;
										}
										break;
									case Dkplus_Model_Criteria::WHERE_LIKE:
										if(
											preg_match('/(.*)' . preg_quote($partWhere['value']) . '/iU', 
												$data[$partWhere['col']])
										){
											$blnPartWhereIsOk = true;
											break;
										}
										break;
									case Dkplus_Model_Criteria::WHERE_NOT_LIKE:
										if(
											!preg_match('/(.*)' . preg_quote($partWhere['value']) . '/iU', 
												$data[$partWhere['col']])
										){
											$blnPartWhereIsOk = true;
											break;
										}
										break;
								}
							}
						}
						if(
							!$blnPartWhereIsOk
						){
							$blnWhereIsOk = false;
						}
					}
					
					
					
					if(
						$blnWhereIsOk
					){
						if(
							is_null($limitStart)
							|| $limitStart <= $i
						){
							if(
								$i < $limitCount
								|| is_null($limitCount)
							){
								$newData[] = $data;
							}
							else{
								break;
							}
						}
						++$i;
					}
				}
			}
		}
		//Nur die Limit-Klausel wird ausgeführt:
		else{
			foreach($this->_data AS $data){
				if(
					is_null($limitStart)
					|| $limitStart <= $i
				){
					if(
						$i < $limitCount
						|| is_null($limitCount)
					){
						$newData[] = $data;
					}
					else{
						break;
					}
				}
				++$i;
			}
		}
		$this->_data = $newData;
	}
	
}