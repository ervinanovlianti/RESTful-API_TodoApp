<?php

namespace App\ContohBootcamp\Services;

use App\ContohBootcamp\Repositories\TaskRepository;

class TaskService {
	private TaskRepository $taskRepository;

	public function __construct() {
		$this->taskRepository = new TaskRepository();
	}

	/**
	 * NOTE: untuk mengambil semua tasks di collection task
	 */
	public function getTasks()
	{
		$tasks = $this->taskRepository->getAll();
		return $tasks;
	}

	/**
	 * NOTE: menambahkan task
	 */
	public function addTask(array $data)
	{
		$taskId = $this->taskRepository->create($data);
		return $taskId;
	}

	/**
	 * NOTE: UNTUK mengambil data task
	 */
	public function getById(string $taskId)
	{
		$task = $this->taskRepository->getById($taskId);
		return $task;
	}

	/**
	 * NOTE: untuk update task
	 */
	public function updateTask(array $editTask, array $formData)
	{
		if(isset($formData['title']))
		{
			$editTask['title'] = $formData['title'];
		}

		if(isset($formData['description']))
		{
			$editTask['description'] = $formData['description'];
		}

		$id = $this->taskRepository->save( $editTask);
		return $id;
	}
	public function deleteTask(string $taskId)
	{
		try {
			$task = $this->taskRepository->getById($taskId);

			if (!$task) {
				return null;
			}

			$this->taskRepository->delete($taskId);
			return true;
		} catch (\Throwable $th) {
			throw new \Exception("Gagal Menghapus Task: ". $th->getMessage());
		}
	}
	// public function assignTask($taskId, $assigned)
	// {
	// 	try {
	// 		// Mengambil task dari repository
	// 		$task = $this->taskRepository->getById($taskId);

	// 		if (!$task) {
	// 			throw new \Exception('Task ' . $taskId . ' tidak ditemukan');
	// 		}

	// 		// Melakukan assign task
	// 		$task['assigned'] = $assigned;

	// 		// Menyimpan perubahan ke repository
	// 		$this->taskRepository->save($task);
	// 	} catch (\Exception $e) {
	// 		throw new \Exception("Gagal mengassign task: " . $e->getMessage());
	// 	}
	// }
	public function assignTask($taskId, $assigned)
	{
		try {
			// Mengambil task dari repository
			$task = $this->taskRepository->getById($taskId);

			if (!$task) {
				throw new \Exception('Task ' . $taskId . ' tidak ditemukan');
			}

			// Melakukan assign task
			$task['assigned'] = $assigned;

			// Menyimpan perubahan ke repository
			$this->taskRepository->save($task);
		} catch (\Exception $e) {
			throw new \Exception("Gagal mengassign task: " . $e->getMessage());
		}
	}
	public function unassignTask($taskId)
	{
		try {
			// Mengambil task dari repository
			$task = $this->taskRepository->getById($taskId);

			if (!$task) {
				throw new \Exception('Task ' . $taskId . ' tidak ditemukan');
			}

			// Mengubah status assigned menjadi null
			$task['assigned'] = null;

			// Menyimpan perubahan ke repository
			$this->taskRepository->save($task);
		} catch (\Exception $e) {
			throw new \Exception("Gagal melakukan unassign task: " . $e->getMessage());
		}
	}
	public function createSubtask($taskId, $title, $description)
	{
		try {
			// Mengambil task dari repository
			$task = $this->taskRepository->getById($taskId);

			if (!$task) {
				throw new \Exception('Task ' . $taskId . ' tidak ditemukan');
			}

			// Membuat subtask
			$subtask = [
				'_id' => (string) new \MongoDB\BSON\ObjectId(),
				'title' => $title,
				'description' => $description,
			];

			// Menambahkan subtask ke daftar subtasks pada task
			$task['subtasks'][] = $subtask;

			// Menyimpan perubahan ke repository
			$this->taskRepository->save($task);

			return $subtask;
		} catch (\Exception $e) {
			throw new \Exception("Gagal membuat subtask: " . $e->getMessage());
		}
	}
	public function deleteSubtask($taskId, $subtaskId)
	{
		try {
			// Mengambil task dari repository
			$task = $this->taskRepository->getById($taskId);

			if (!$task) {
				throw new \Exception('Task ' . $taskId . ' tidak ditemukan');
			}

			// Menghapus subtask berdasarkan subtask_id
			$task['subtasks'] = array_filter($task['subtasks'], function ($subtask) use ($subtaskId) {
				return $subtask['_id'] !== $subtaskId;
			});

			// Menyimpan perubahan ke repository
			$this->taskRepository->save($task);
		} catch (\Exception $e) {
			throw new \Exception("Gagal menghapus subtask: " . $e->getMessage());
		}
	}
}