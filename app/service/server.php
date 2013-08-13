<?php

/*
Copyright 2010 Ludovico Fischer. All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:
1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list
   of conditions and the following disclaimer in the documentation and/or other materials
   provided with the distribution.

THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

class Server {
    /* The array key works as id and is used in the URL
       to identify the resource.
    */
    private $songs = array();

    private $db = null;
    private $table = null;

    public function __construct(&$db, $table){
      $this->db = $db;
      $this->table = $table;

      $this->db->connect();
    }

    public function serve() {

        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $paths = explode('/', $this->paths($uri));
        array_shift($paths); // Hack; get rid of initials empty string
        $resource = array_shift($paths);

        if ($resource == 'clients') {
            $id = array_shift($paths);

            if (empty($id)) {
                $this->handle_base($method);
            } else {
                $this->handle_id($method, $id);
            }

        } else {
            // We only handle resources under 'clients'
            header('HTTP/1.1 404 Not Found');
        }
    }

    private function handle_base($method) {
        switch($method) {
        case 'GET':
            $q = "SELECT * from " . $this->table;
            $this->songs = $this->db->select($q);
            $this->result();
            break;
        default:
            header('HTTP/1.1 405 Method Not Allowed');
            header('Allow: GET');
            break;
        }
    }

    private function handle_id($method, $id) {
        switch($method) {
        case 'PUT':
            $this->create_song($id);
            break;

        case 'DELETE':
            $this->delete_song($id);
            break;

        case 'GET':
            $this->display_song($id);
            break;

        default:
            header('HTTP/1.1 405 Method Not Allowed');
            header('Allow: GET, PUT, DELETE');
            break;
        }
    }

    private function create_song($id){
        if (isset($this->songs[$id])) {
            header('HTTP/1.1 409 Conflict');
            return;
        }
        /* PUT requests need to be handled
         * by reading from standard input.
         */
        $data = json_decode(file_get_contents('php://input'));
        if (is_null($data)) {
            header('HTTP/1.1 400 Bad Request');
            $this->result();
            return;
        }
        $this->songs[$id] = $data;
        $this->result();
    }

    private function delete_song($id) {
        if (isset($this->songs[$id])) {
            unset($this->songs[$id]);
            $this->result();
        } else {
            header('HTTP/1.1 404 Not Found');
        }
    }

    private function display_song($id) {
        if (array_key_exists($id, $this->songs)) {
            echo json_encode($this->songs[$id]);
        } else {
            header('HTTP/1.1 404 Not Found');
        }
    }

    private function paths($url) {
        $uri = parse_url($url);
        return $uri['path'];
    }

    /**
     * Displays a list of all songs.
     */
    private function result() {
        header('Content-type: application/json');
        echo json_encode($this->songs);
    }
  }


?>
