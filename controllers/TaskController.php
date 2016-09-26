<?php

class TaskController {

    static function sortTasks($tasks) {
        $tree = [];
        foreach ($tasks as $key => $onetask) {
            if($onetask["parent"] == 0) {
                $onetask["subtasks"] = self::getTasks($onetask, $tasks);
                $tree[] = $onetask;
            }
        }
        return $tree;
    }

    static function getTasks($task, $tasks) {
        $subtasks = [];
        foreach ($tasks as $key => $onetask) {
            if($onetask["parent"] == $task["id"]) {
                $subtask = $tasks[$key];
                $subtask["subtasks"] = self::getTasks($subtask, $tasks);
                $subtasks[] = $subtask;
            }
        }
        return $subtasks;
    }

    /**
     * Display a listing of the resource via API.
     *
     * @return string
     */
    static function tasks() {
        $filterMap = ["all" => -1, "done" => 0, "active" => 1];
        $filter = isset($filterMap[real_escape_string($_GET["state"])]) ? $filterMap[real_escape_string($_GET["state"])] : $filterMap["all"];
        $where = $filter==-1 ? 'status >= 0' : ($filter==1 ? 'status <> 0' : 'status = 0');
        $sql = "SELECT * FROM tasks WHERE $where AND user_id = ".Auth::user()->id;
        $pureList = DB::query($sql, true);
        $tasks = self::sortTasks($pureList);
        $result = array('status' => count($tasks)>0);
        if(count($tasks)) {
            $result['data'] = $tasks;
        } else {
            $result['error'] = 'Tasks not found';
        }
        echo json_encode($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  array  $params
     */
    function save($params) {
        $id = is_numeric($params["id"]) ? $params["id"] : 0;
        $data = getJson();
        $newname = !empty($data['name']) ? real_escape_string($data['name']) : null;
        if($newname) {
            $sql = "SELECT * FROM tasks WHERE id = $id AND user_id = ".Auth::user()->id;
            $task = DB::query($sql, true);
            if(count($task)) {
                $sql = "UPDATE tasks SET name = '$newname' WHERE id = $id AND user_id = ".Auth::user()->id;
                $result = DB::query($sql);
                echo json_encode([ "result" => $result]);
            } else {
                echo json_encode([ "result" => false, "error" => "Task not found." ]);
            }
        } else {
            echo json_encode([ "result" => false, "error" => "You have to specify new name of a task." ]);
        }
    }

    /**
     * Change status of the specified resource in storage.
     *
     * @param  array  $params
     */
    function changeState($params) {
        $id = is_numeric($params["id"]) ? $params["id"] : 0;
        $data = getJson();
        $newstate = ''.is_numeric($data["state"]) ? $data["state"] : 0;
        if($newstate !== null) {
            $sql = "SELECT * FROM tasks WHERE id = $id AND user_id = ".Auth::user()->id;
            $task = DB::query($sql, true);
            if(count($task)) {
                $sql = "UPDATE tasks SET status = $newstate WHERE id = $id AND user_id = ".Auth::user()->id;
                $result = DB::query($sql);
                echo json_encode([ "result" => $result]);
            } else {
                echo json_encode([ "result" => false, "error" => "Task not found." ]);
            }
        } else {
            echo json_encode([ "result" => false, "error" => "You have to specify the state of a task." ]);
        }
    }

    /**
     * Store the specified resource in storage.
     *
     */
    function add() {
        $data = getJson();
        $parent = !empty($data['parent']) ? real_escape_string($data['parent']) : 0;
        $name = !empty($data['name']) ? real_escape_string($data['name']) : null;
        $description = !empty($data['description']) ? real_escape_string($data['description']) : '';
        if($name) {
            $sql = "INSERT INTO tasks (parent, user_id, name, description) VALUES ($parent, ".Auth::user()->id.", '$name', '$description')";
            $result = DB::query($sql);
            echo json_encode([ "result" => $result, "data" => array(
                "id" => DB::last_id(),
                "parent" => $parent,
                "status" => 1,
                "user_id" => Auth::user()->id,
                "name" => $name,
                "description" => $description
            ) ]);
        } else {
            echo json_encode([ "result" => $result, "error" => "You have to specify the name of a task." ]);
        }
    }

    /**
     * Delete the specified resource in storage.
     *
     */
    function delete() {
        $data = getJson();
        $ids = real_escape_string(implode(',', $data));
        if(count($data)) {
            $sql = "DELETE FROM tasks WHERE id IN ($ids) AND user_id = ".Auth::user()->id;
            DB::query($sql);
            echo json_encode([ "result" => true ]);
        } else {
            echo json_encode([ "result" => false, "error" => "You have to specify the tasks to delete." ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    function index() {
        render("list");
    }

}