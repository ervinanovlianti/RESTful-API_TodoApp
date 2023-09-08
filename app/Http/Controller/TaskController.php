<?php

namespace App\Http\Controller;

use App\ContohBootcamp\Services\TaskService;
use App\Helpers\MongoModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller {
	private TaskService $taskService;
	public function __construct() {
		$this->taskService = new TaskService();
	}

	public function showTasks()
	{
		$tasks = $this->taskService->getTasks();
		return response()->json($tasks);
	}

	public function createTask(Request $request)
	{
		$request->validate([
			'title'=>'required|string|min:3',
			'description'=>'required|string'
		]);

		$data = [
			'title'=>$request->post('title'),
			'description'=>$request->post('description')
		];

		$dataSaved = [
			'title'=>$data['title'],
			'description'=>$data['description'],
			'assigned'=>null,
			'subtasks'=> [],
			'created_at'=>time()
		];

		$id = $this->taskService->addTask($dataSaved);
		$task = $this->taskService->getById($id);

		return response()->json($task);
	}

	public function updateTask(Request $request)
	{
		$request->validate([
			'task_id'=>'required|string',
			'title'=>'string',
			'description'=>'string',
			'assigned'=>'string',
			'subtasks'=>'array',
		]);

		$taskId = $request->post('task_id');
		$formData = $request->only('title', 'description', 'assigned', 'subtasks');
		$task = $this->taskService->getById($taskId);

		$this->taskService->updateTask($task, $formData);

		$task = $this->taskService->getById($taskId);

		return response()->json($task);
	}

	// TODO: deleteTask()
	public function deleteTask(Request $request)
	{
		$request->validate([
			'task_id' => 'required|string',
		]);

		$taskId = $request->input('task_id');

		try {
			$result = $this->taskService->deleteTask($taskId);

			if ($result === null) {
				return response()->json([
					'message' => 'Task ' . $taskId . ' tidak ditemukan',
				], 404);
			}

			return response()->json([
				'message' => 'Task ' . $taskId . ' berhasil dihapus',
			]);
		} catch (\Exception $e) {
			return response()->json([
				'message' => 'Terjadi kesalahan saat menghapus task: ' . $e->getMessage(),
			], 500);
		}
	}

	// TODO: assignTask()
	public function assignTask(Request $request)
	{
		$request->validate([
			'task_id' => 'required|string',
			'assigned' => 'required|string',
		]);

		$taskId = $request->input('task_id');
		$assigned = $request->input('assigned');

		try {
			// Mengambil task menggunakan metode getById dari TaskService
			$task = $this->taskService->getById($taskId);

			if (!$task) {
				return response()->json([
					'message' => 'Task ' . $taskId . ' tidak ditemukan',
				], 404);
			}

			// Memanggil metode assignTask dari TaskService
			$this->taskService->assignTask($taskId, $assigned);

			// Mengambil ulang task setelah assignTask dilakukan
			$task = $this->taskService->getById($taskId);

			return response()->json($task);
		} catch (\Exception $e) {
			return response()->json([
				'message' => 'Terjadi kesalahan saat mengassign task: ' . $e->getMessage(),
			], 500);
		}
	}

	// TODO: unassignTask()
	public function unassignTask(Request $request)
	{
		$request->validate([
			'task_id' => 'required|string',
		]);

		$taskId = $request->input('task_id');

		try {
			// Mengambil task menggunakan metode getById dari TaskService
			$task = $this->taskService->getById($taskId);

			if (!$task) {
				return response()->json([
					'message' => 'Task ' . $taskId . ' tidak ditemukan',
				], 404);
			}

			// Memanggil metode unassignTask dari TaskService
			$this->taskService->unassignTask($taskId);

			// Mengambil ulang task setelah unassignTask dilakukan
			$task = $this->taskService->getById($taskId);

			return response()->json($task);
		} catch (\Exception $e) {
			return response()->json([
				'message' => 'Terjadi kesalahan saat melakukan unassign task: ' . $e->getMessage(),
			], 500);
		}
	}

	// TODO: createSubtask()
	public function createSubtask(Request $request)
	{
		$request->validate([
			'task_id' => 'required|string',
			'title' => 'required|string',
			'description' => 'required|string',
		]);

		$taskId = $request->input('task_id');
		$title = $request->input('title');
		$description = $request->input('description');

		try {
			// Memanggil metode createSubtask dari TaskService
			$subtask = $this->taskService->createSubtask($taskId, $title, $description);

			return response()->json($subtask);
		} catch (\Exception $e) {
			return response()->json([
				'message' => 'Terjadi kesalahan saat membuat subtask: ' . $e->getMessage(),
			], 500);
		}
	}


	// TODO deleteSubTask()
	public function deleteSubtask(Request $request)
	{
		$request->validate([
			'task_id' => 'required|string',
			'subtask_id' => 'required|string',
		]);

		$taskId = $request->input('task_id');
		$subtaskId = $request->input('subtask_id');

		try {
			// Memanggil metode deleteSubtask dari TaskService
			$this->taskService->deleteSubtask($taskId, $subtaskId);

			return response()->json([
				'message' => 'Subtask ' . $subtaskId . ' berhasil dihapus',
			]);
		} catch (\Exception $e) {
			return response()->json([
				'message' => 'Terjadi kesalahan saat menghapus subtask: ' . $e->getMessage(),
			], 500);
		}
	}
	// public function deleteSubtask(Request $request)
	// {
	// 	$request->validate([
	// 		'task_id' => 'required|string',
	// 		'subtask_id' => 'required|string',
	// 	]);

	// 	$taskId = $request->input('task_id');
	// 	$subtaskId = $request->input('subtask_id');

	// 	try {
	// 		// Memanggil metode deleteSubtask dari TaskService
	// 		$task = $this->taskService->deleteSubtask($taskId, $subtaskId);

	// 		if (!$task) {
	// 			return response()->json([
	// 				'message' => 'Task ' . $taskId . ' tidak ditemukan',
	// 			], 404);
	// 		}

	// 		return response()->json($task);
	// 	} catch (\Exception $e) {
	// 		return response()->json([
	// 			'message' => 'Terjadi kesalahan saat menghapus subtask: ' . $e->getMessage(),
	// 		], 500);
	// 	}
	// }
	// public function deleteSubtask(Request $request)
	// {
	// 	$mongoTasks = new MongoModel('tasks');
	// 	$request->validate([
	// 		'task_id'=>'required',
	// 		'subtask_id'=>'required'
	// 	]);

	// 	$taskId = $request->post('task_id');
	// 	$subtaskId = $request->post('subtask_id');

	// 	$existTask = $mongoTasks->find(['_id'=>$taskId]);

	// 	if(!$existTask)
	// 	{
	// 		return response()->json([
	// 			"message"=> "Task ".$taskId." tidak ada"
	// 		], 401);
	// 	}

	// 	$subtasks = isset($existTask['subtasks']) ? $existTask['subtasks'] : [];

	// 	// Pencarian dan penghapusan subtask
	// 	$subtasks = array_filter($subtasks, function($subtask) use($subtaskId) {
	// 		if($subtask['_id'] == $subtaskId)
	// 		{
	// 			return false;
	// 		} else {
	// 			return true;
	// 		}
	// 	});
	// 	$subtasks = array_values($subtasks);
	// 	$existTask['subtasks'] = $subtasks;

	// 	$mongoTasks->save($existTask);

	// 	$task = $mongoTasks->find(['_id'=>$taskId]);

	// 	return response()->json($task);
	// }

}