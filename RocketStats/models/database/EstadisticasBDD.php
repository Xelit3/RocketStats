<?php
require_once 'models/beans/EstadisticasJugador.php';
require_once 'models/beans/EstadisticasPartido.php';
require_once 'models/beans/ControlEstadisticasJugador.php';

class EstadisticasBDD{
	
	private $INSERT_INTO_ESTADISTICAS_PARTIDOS = "INSERT INTO estadisticas_partido (tipo_partido, id_jugador, victoria, mvp, puntaje, goles, asistencias, salvadas, tiros, auditoria_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
	private $GET_STATS_BY_TYPE_MATH = "SELECT id_jugador, count(*) AS total_partidos, sum(victoria) AS total_victorias, sum(mvp) AS total_mvps, 
			sum(puntaje) AS total_puntaje, avg(puntaje) AS media_puntaje, sum(goles) AS total_goles, avg(goles) AS media_goles, sum(asistencias) AS total_asistencias, avg(asistencias) AS media_asistencias, 
			sum(salvadas) AS total_salvadas, avg(salvadas) AS media_salvadas, sum(tiros) AS total_tiros, avg(tiros) AS media_tiros from estadisticas_partido ";
	private $GET_STATS_BY_TYPE_MATH_WHERE_CONDITION = "where tipo_partido = ? ";
	private $GET_STATS_BY_TYPE_MATH_GROUPBY_CONDITION = "group by id_jugador";
	
	public function insertPartido($partido){
		$arrayVectorData = array();
		
		foreach ($partido->getEstadisticas() as $estadisticaPartido){
			$tmpVector = array(
				$partido->getTipo(),
				$estadisticaPartido->getJugador(),
				$estadisticaPartido->getVictoria(),
				$estadisticaPartido->getMvp(),
				$estadisticaPartido->getPuntaje(),
				$estadisticaPartido->getGoles(),
				$estadisticaPartido->getAsistencias(),
				$estadisticaPartido->getSalvadas(),
				$estadisticaPartido->getTiros(),
				$_SESSION["sessionUserId"]
			);
			
			array_push($arrayVectorData, $tmpVector);
		}
		
		$con = new BDDConexion();
		$con->executeMultipleQueries($this->INSERT_INTO_ESTADISTICAS_PARTIDOS, $arrayVectorData);

		return "OK";
	}
	
	public function getEstadisticasPorTipoPartido($tipoPartido){
		$estadisticasJugadores = array();
		
		$vector = array ();
		$con = new BDDConexion();
		
		$tmpSql = $this->GET_STATS_BY_TYPE_MATH;
		
		if($tipoPartido > 0 && $tipoPartido < 5){
			$tmpSql = $tmpSql . $this->GET_STATS_BY_TYPE_MATH_WHERE_CONDITION;
			array_push($vector, $tipoPartido);
		}
		$tmpSql = $tmpSql . $this->GET_STATS_BY_TYPE_MATH_GROUPBY_CONDITION;
		
		$resultado = $con->launchQuery($tmpSql, $vector);
		
		if ($resultado != null) {
			$estadisticasJugador = null;
			foreach($resultado as $row){
				$estadisticasJugador = new ControlEstadisticasJugador();
				
				$estadisticasJugador->idJugador = $row['id_jugador'];
				$estadisticasJugador->totalPartidos = $row['total_partidos'];
				$estadisticasJugador->totalVictorias = $row['total_victorias'];
				$estadisticasJugador->totalMvps = $row['total_mvps'];
				$estadisticasJugador->totalPuntaje = $row['total_puntaje'];
				$estadisticasJugador->mediaPuntaje = $row['media_puntaje'];
				$estadisticasJugador->totalGoles = $row['total_goles'];
				$estadisticasJugador->mediaGoles = $row['media_goles'];
				$estadisticasJugador->totalAsistencias = $row['total_asistencias'];
				$estadisticasJugador->mediaAsistencias = $row['media_asistencias'];
				$estadisticasJugador->totalSalvadas = $row['total_salvadas'];
				$estadisticasJugador->mediaSalvadas = $row['media_salvadas'];
				$estadisticasJugador->totalTiros = $row['total_tiros'];
				$estadisticasJugador->mediaTiros = $row['media_tiros'];
				
				array_push($estadisticasJugadores, $estadisticasJugador);
			}
		}
		
		return $estadisticasJugadores;
	}
		
}

?>