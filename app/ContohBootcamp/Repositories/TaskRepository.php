<?php
namespace App\ContohBootcamp\Repositories;

use App\Helpers\MongoModel;

class TaskRepository
{
	private MongoModel $tasks;
	public function __construct()
	{
		$this->tasks = new MongoModel('tasks');
	}

	/**
	 * Untuk mengambil semua tasks
	 */
	public function getAll()
	{
		$tasks = $this->tasks->get([]);
		return $tasks;
	}

	/**
	 * Untuk mendapatkan task bedasarkan id
	 *  */
	public function getById(string $id)
	{
		$task = $this->tasks->find(['_id'=>$id]);
		return $task;
	}

	/**
	 * Untuk membuat task
	 */
	public function create(array $data)
	{
		$dataSaved = [
			'title'=>$data['title'],
			'description'=>$data['description'],
			'assigned'=>null,
			'subtasks'=> [],
			'created_at'=>time()
		];

		$id = $this->tasks->save($dataSaved);
		return $id;
	}

	/**
	 * Untuk menyimpan task baik untuk membuat baru atau menyimpan dengan struktur bson secara bebas
	 *  */
	public function save(array $editedData)
	{
		$id = $this->tasks->save($editedData);
		return $id;
	}

	/**
	 * Untuk mendapatkan menghapus task berdasarkan id
	 *  */
	public function delete(string $id)
	{
		try {
			$this->tasks->deleteQuery(['_id' => $id]);
		} catch (\Exception $th) {
			throw new \Exception("Gagal menghapus task". $th->getMessage());
		}
	}
	/**
	 * Assign Task
	 *  */
	public function assign($id, $assigned){
		try {
			$this->tasks->save(
				['_id' => $id],
				['$set' => ['assigned' => $assigned]]
			);
		} catch (\Exception $e) {
			throw new \Exception("Gagal mengassign task dalam repository: " . $e->getMessage());
		}
	}

	
}