<?php
namespace App\Service\Repartidor;

use App\Repository\Repartidor\RepartidorRepository;
use App\Model\Repartidor\Repartidor; // Asegúrate de que el namespace del modelo sea correcto

class RepartidorService {
    private $repartidorRepository;

    public function __construct(RepartidorRepository $repartidorRepository) {
        $this->repartidorRepository = $repartidorRepository;
    }

    /**
     * Inserta un repartidor utilizando los datos proporcionados.
     *
     * @param array $data Datos del repartidor (por ejemplo: IdTurno, IdRepartidor, Transporte, etc.)
     * @return mixed Resultado de la operación en el repositorio
     * @throws \Exception Si falta algún dato o falla la inserción
     */
    public function insertVehiculo(array $data) {
        // Validar que todos los campos necesarios estén presentes
        $requiredFields = ['IdRepartidor', 'Transporte', 'Placas', 'KilosTortillaAsignados', 'HoraInicio', 'HoraFin'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \Exception("El campo $field es requerido.");
            }
        }
    
        // Crear una instancia del modelo Repartidor
        $repartidor = new Repartidor(
            $data['IdRepartidor'],
            $data['Transporte'],
            $data['Placas'],
            $data['KilosTortillaAsignados'],
            $data['HoraInicio'],
            $data['HoraFin']
        );
    
        // Llama al método del repositorio para insertar el repartidor
        $idInsertado = $this->repartidorRepository->setVehiculo($repartidor);
    
        return [
            'success' => true,
            'message' => 'Repartidor insertado correctamente.',
            'id' => $idInsertado
        ];
    }
}
