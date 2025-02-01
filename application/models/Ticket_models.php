<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ticket_models extends CI_Model {

    public function daftar_tugas($id_user) {
        $this->db->where('assigned_to', $id_user);
        return $this->db->get('tickets');
    }

    public function get_new_assigned_tickets($id_user, $last_checked) {
        $this->db->where('assigned_to', $id_user);
        $this->db->where('assigned_at >', $last_checked);
        return $this->db->get('tickets')->result();
    }
}
