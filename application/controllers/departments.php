<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Departments extends CI_Controller {

	public function __construct()
 	{
 		parent::__construct();
 	}

 	public function index () {

        
        $add_dept_error_msg = NULL;
        $add_dept_error_action = NULL; 
        $reg_error_msg = NULL;
        
        //check kung naka-login
		if($this->session->userdata('is_logged_in')) {

			$data = array (
				'current_user' => $this->session->userdata('displayname'),
				'current_username' => $this->session->userdata('username'),
				'add_dept_error_msg' => $add_dept_error_msg,
				'add_dept_error_action' => $add_dept_error_action

			);

			$this->load->model('department_model');

            if($query = $this->department_model->list_department()) {
                $data['records'] = $query;
            }

			$this->load->view('includes/nocache');
	        $this->load->view('includes/header2');
	        $this->load->view('departments_view', $data);
	        $this->load->view('includes/department_footer');

			
		}

		else {
			
			//kung hindi naka-login balik sa main page
			redirect(base_url(), 'refresh');
		}
 	}

 	public function add_department() {

        $this->form_validation->set_rules('addCode','Department Code','trim|required');
        $this->form_validation->set_rules('addDesc','Department Description','trim|required');
        

        if($this->form_validation->run() == TRUE) {
            $data = array (
                'department_code' => $this->input->post('addCode'),
                'department_desc' => $this->input->post('addDesc'),
                'userid' => $this->session->userdata('userid')   
            );


            $this->load->model('department_model');

            $this->department_model->add_department($data);

            $this->session->unset_userdata('add_dept_error_msg');
            $this->session->unset_userdata('add_dept_error_action');

            redirect(base_url() . 'index.php/departments', 'refresh');  
            
            
        }

        else
        {

            $add_dept_error_msg = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' . validation_errors() . '</div>';
            $add_dept_error_action = "$('#modalAddDepartment').modal('show');";

            $data = array (
                'current_user' => $this->session->userdata('displayname'),
                'current_username' => $this->session->userdata('username'),
                'add_dept_error_msg' => $add_dept_error_msg,
                'add_dept_error_action' => $add_dept_error_action
            );

            $this->load->model('department_model');

            if($query = $this->department_model->list_department()) {
                $data['records'] = $query;
            } else {
                $data['records'] = $query;
            }

            if(!$data['records']) {
                //$add_dept_error_msg = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>The record is existing!</div>';
                $add_dept_error_action = "$('#modalAddDepartment').modal('show');";
                $data = array (
                    'current_user' => $this->session->userdata('displayname'),
                    'current_username' => $this->session->userdata('username'),
                    'add_dept_error_msg' => $add_dept_error_msg,
                    'add_dept_error_action' => $add_dept_error_action
                );

                $this->session->set_userdata($data);
            }
             
            $this->load->view('includes/nocache');
            $this->load->view('includes/header2');
            $this->load->view('departments_view', $data);
            $this->load->view('includes/department_footer', $data);

            
        }
        
    }

    public function list_edit_department (
            $add_dept_error_msg = NULL, 
            $add_dept_error_action = "$('#modalEditDepartment').modal('show');", 
            $reg_error_msg = NULL

        ) {

        $data = array (
            'userid' => $this->session->userdata('userid')
        );


        $this->load->model('department_model');
        //kinuha lang ung department id galing sa view
        $department_id = $this->input->post('dataid');

        //check kung ajax request
        if($this->input->post('ajax')) {

            //ni-recycle ko lng ung sa addsem na modal trigger
            $add_dept_error_action = "$('#modalEditDepartment').modal('show');"; 

            $query = $this->department_model->get_dept_code($department_id);
            $query2 = $this->department_model->get_dept_desc($department_id);

            $data = array (
                'current_user' => $this->session->userdata('displayname'),
                'current_username' => $this->session->userdata('username'),
                'add_dept_error_msg' => $add_dept_error_msg,
                'add_dept_error_action' => $add_dept_error_action,
                'dataid' => $department_id,
                'dcode' => $query,
                'ddesc' => $query2
                
            );

            $this->session->set_userdata($data); //the trick!! mawawala na ung ajax request next time eh
            
            echo implode('', $data['dcode']);
            echo '*';
            echo implode('', $data['ddesc']);
        } else {

            $this->form_validation->set_rules('editCode','Department Code','trim|required');
            $this->form_validation->set_rules('editDesc','Department Description','trim|required');
            
            if($this->form_validation->run() == TRUE) {

                $code = $this->input->post('editCode');
                $desc = $this->input->post('editDesc');

                //kinuha ung session ng dept id
                $department_id = $this->session->userdata('dataid');
                $this->department_model->update_department($department_id, $code, $desc);
                echo $department_id.$code.$desc; //for debugging purpose
                redirect(base_url().'index.php/departments');
            }
            else {
                redirect(base_url().'index.php/departments');
            }
        }

    }

    public function delete_department ($id = NULL) {

        $this->load->model('department_model');
        $this->department_model->delete_department($id);
        redirect(base_url().'index.php/departments');
        return;
    }

    public function delete_all_department () {

        $this->load->model('department_model');
        $this->department_model->delete_all_department();
        redirect(base_url().'index.php/departments');
        return;
    }

}