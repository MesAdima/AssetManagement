<?php
if(!defined("BASEPATH")) exit("No direct access to the script is allowed");

class Tenant extends MY_Controller
{
	var $active_groups;
	function __construct()
	{
		parent:: __construct();
		$this->load->model('m_tenant');
	}
	function index()
	{
		$data['content_page'] = 'tenant/tenants';
		$data['sidebar'] = 'hr_side_bar';
		$data[''] = $this->m_tenant->get_tenants();
		$data['all_tenants'] = $this->all_tenants();
		// echo "<pre>";print_r($data);die();
		$this->template->call_template($data);
	}

	function registration()
	{
		$tenant_first_name = $this->input->post('tenantfname');
		$tenant_last_name = $this->input->post('tenantlname');
		$national_passport = $this->input->post('nationalpass');
		$phone_number = $this->input->post('phonenumber');
		$tenant_status = $this->input->post('status');

		$insert = $this->m_tenant->register_tenant($tenant_first_name, $tenant_last_name, $national_passport, $phone_number, $tenant_status);

		if ($insert) {
			echo "Insertion complete";
		} else {
			echo "Error occured";
		}
		
	}

	function all_tenants()
	{
		$active_job_groups = $this->m_tenant->get_tenants();
		// echo "<pre>";print_r($active_job_groups);die();
		$count = 0;
		$this->active_groups .= "<tbody>";
		if ($active_job_groups == NULL) {
			$this->active_groups .= '<tr>';
			$this->active_groups .= '<td colspan="4"><center>No record found in the database...</center></td>';
			$this->active_groups .= '</tr>';
		} else {
			foreach ($active_job_groups as $key => $value) {
				if ($value['status'] == 1) {
					$span = '<span></span>';
				} else if ($value['status'] == 0) {
					$span = '<span></span>';
				}
				$count++;
				$this->active_groups .= '<tr>';
				$this->active_groups .= '<td>'.$count.'</td>';
				$this->active_groups .= '<td>'.$value['firstname'].'</td>';
				$this->active_groups .= '<td>'.$value['lastname'].'</td>';
				$this->active_groups .= '<td>'.$value['nationalid_passport'].'</td>';
				$this->active_groups .= '<td>'.$value['phone_number'].'</td>';
				$this->active_groups .= '<td>'.$span.'</td>';
				$this->active_groups .= '<td>'.$value['date_registered'].'</td>';
				
				$this->active_groups .= '</tr>';
			}
		}
		
		$this->active_groups .= "</tbody>";

		return $this->active_groups;
	}


	public function edittenant()
	{
		$id = $this->input->post('editid');
		$tenant_first_name = $this->input->post('edittenantfname');
		$tenant_last_name = $this->input->post('edittenantlname');
		$national_passport = $this->input->post('editnationalpass');
		$phone_number = $this->input->post('editphonenumber');
		$tenant_status = $this->input->post('editstatus');
		
		$sql = "UPDATE
					`tenant`
				SET
				    'firstname' => $tenant_first_name,
						'lastname' 	=> $tenant_last_name,
						'nationalid_passport' 	=> $national_passport,
						'phone_number' 	=> $phone_number,
						'status' 	=> $tenant_status
					
				WHERE
					`tenant_id` = '$id'";
		$this->db->query($sql);

		$this->index();
		
	}

	public function searchtenant()
	{
		$search_array = array();
		if($this->input->post())
		{
			foreach ($this->input->post() as $key => $value) {
				if($value)
				{
					
					$data[$key] = $value;
				}
				else
				{
					$data = array();
				}
			}

			$query = $this->db->get_where('tenant', $data);

			$result = $query->result_array();
			$search_array = $result;
		}

		return $search_array;
	}

	public function generate_search_table()
	{
		$tenant_list = '';
		$data = $this->searchtenant();
		$tenant_list .= '<thead><tr><th>#</th><th>Tenant Number</th><th>First Name</th><th>Last Name</th><th>National ID / Passport No</th><th>Phone Number</th><th>Status</th><th>Date Registered</td></tr></thead>';
		$tenant_list .= '<tbody>';
		if($data)
		{
			$counter = 0;
			foreach ($data as $key => $value) {
				$counter++;
				$tenant_list .= '<tr>';
				$tenant_list .= '<td>' . $counter. '</td>';
				$tenant_list .= '<td>' . $value['firstname'] . '</td>';
				$tenant_list .= '<td>' . $value['lastname'] . '</td>';
				$tenant_list .= '<td>' . $value['nationalid_passport'] . '</td>';
				$tenant_list .= '<td>' . $value['phone_number'] . '</td>';
				$tenant_list .= '<td>' . $value['status'] . '</td>';
				$tenant_list .= '<td>' . $value['date_registered'] . '</td>';
				$tenant_list .= '<td><a href = "'.base_url().'tenant/search/tenantmember/' . $value['tenant_id'] . '">View More</a></td>';
				$tenant_list .= '</tr>';
			}
		}
		else
		{
			$tenant_list .= '<tr><td colspan = "7"><center>No data found</center></td></tr>';
		}
		$tenant_list .= '</tbody>';

		return $tenant_list;
	}

	public function tenantmember($tenant_id)
	{
		$tenant_details = $this->m_tenant->get_tenant_searched($tenant_id);
		if ($tenant_details) {
			# code...
		}
	}

	public function searchresult()
	{
		$data['content_page'] = 'tenant/tenants';
		$data['sidebar'] = 'hr_side_bar';
		$data['search_result'] = $this->generate_search_table();
		
		$this->template->call_template($data);
	}

}
?>