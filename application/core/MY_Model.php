<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    public $table;
    public $thead;
    public $childs;

    public function __construct()
    {
        // parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->database();
        $this->load->library('datatables');
        $this->table = strtolower($this->router->class);
        $this->thead = [
            (object) ['mData' => 'name', 'sTitle' => 'Name'],
        ];
        $this->childs = [];
    }

    public function lastSubmit($post)
    {
        if (!$post) {
            return false;
        }
        if ($post['last_submit'] === $this->session->userdata('last_submit')) {
            return false;
        }
        $this->session->set_userdata('last_submit', $post['last_submit']);
        unset($post['last_submit']);
        return $post;
    }

    public function save($record)
    {
        foreach ($record as $field => &$value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            } elseif (strpos($value, '[comma-replacement]') > -1) {
                $value = str_replace('[comma-replacement]', ',', $value);
            }
        }
        return isset($record['uuid']) ? $this->update($record) : $this->create($record);
    }

    public function create($record)
    {
        if ($this->db->field_exists('kode', $this->table)) {
            $record['kode'] = strtoupper(base_convert(time() + rand(), 10, 36));
        }
        $generate = $this->db->select('UUID() uuid', false)->get()->row_array();
        $record['uuid'] = $generate['uuid'];
        $record = $this->savechild($record);
        $record['createdAt'] = date('Y-m-d H:i:s');
        $record['updatedAt'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $record);
        return $record['uuid'];
    }

    public function update($record)
    {
        $record = $this->savechild($record);
        $record['updatedAt'] = date('Y-m-d H:i:s');
        $this->db->where('uuid', $record['uuid'])->update($this->table, $record);
        return $record['uuid'];
    }

    public function findOne($param)
    {
        if (!is_array($param)) {
            $param = ['uuid' => $param];
        }
        $param['deletedAt'] = null;
        return $this->db->get_where($this->table, $param)->row_array();
    }

    public function dt()
    {
        return $this->datatables->from($this->table)->where("{$this->table}.deletedAt", null)->generate();
    }

    public function find($param = [])
    {
        $param['deletedAt'] = null;
        return $this->db->get_where($this->table, $param)->result();
    }

    public function findIn($field, $value)
    {
        return $this->db->where_in($field, $value)->get($this->table)->result();
    }

    public function select2($field, $term)
    {
        return $this->db
            ->select("uuid as id", false)
            ->select("$field as text", false)
            ->where('deletedAt', null)
            ->limit(10)
            ->like($field, $term ?? '')->get($this->table)->result();
    }

    public function delete($uuid)
    {
        foreach ($this->childs as $child) {
            $childmodel = $child['model'];
            $this->load->model($childmodel);
            foreach ($this->$childmodel->find([$this->table => $uuid]) as $childrecord) {
                $this->$childmodel->delete($childrecord->uuid);
            }
        }
        // return $this->db->where('uuid', $uuid)->delete($this->table);
        return $this->db->where('uuid', $uuid)->set('deletedAt', date('Y-m-d H:i:s'))->update($this->table);
    }

    public function getForm($uuid = false, $isSubform = false)
    {
        $form = $uuid ? $this->prepopulate($uuid) : $this->form;

        if ($uuid) {
            $form[] = [
                'name' => 'uuid',
                'type' => 'hidden',
                'value' => $uuid,
                'label' => 'UUID'
            ];
        }

        foreach ($form as &$f) {
            if (!isset($f['attributes'])) {
                $f['attributes']   = [];
            }
            if (isset($f['options'])) {
                $f['type'] = 'select';
            }
            if (isset($f['multiple'])) {
                $f['name'] = $f['name'] . '[]';
                $f['attributes'][] = ['multiple' => 'true'];
            }
            if (!isset($f['type'])) {
                $f['type']   = 'text';
            }
            if (!isset($f['width'])) {
                $f['width'] = 2;
            }
            if (!isset($f['value'])) {
                $f['value']       = '';
            }
            if (!isset($f['required'])) {
                $f['required'] = '';
            } else {
                $f['required'] = 'required="required"';
            }

            $f['disabled'] = !isset($f['disabled']) ? '' : 'disabled="disabled"';

            if (in_array(['data-suggestion' => true], $f['attributes'])) {
                $fname = str_replace('[]', '', $f['name']);
                if (isset($f['multiple'])) {
                    $alloptions = [];
                    $f['options'] = [];
                    foreach ($this->db->select($fname)->get($this->table)->result() as $record) {
                        if (strlen($record->$fname) > 0) {
                            foreach (explode(',', $record->$fname) as $option) {
                                $alloptions[] = $option;
                            }
                        }
                    }
                    foreach (array_unique($alloptions) as $distinct) {
                        $f['options'][] = ['text' => $distinct, 'value' => $distinct];
                    }
                } else {
                    $f['options'] = [];
                    foreach ($this->db->select($fname)->distinct()->get($this->table)->result() as $record) {
                        if (strlen($record->$fname) > 0) {
                            $f['options'][] = ['text' => $record->$fname, 'value' => $record->$fname];
                        }
                    }
                }
            }

            $f['attr'] = '';
            foreach ($f['attributes'] as $attribute) {
                foreach ($attribute as $key => $value) {
                    $f['attr'] .= " $key=\"$value\"";
                }
            }
        }
        return $form;
    }

    public function prepopulate($uuid)
    {
        $record = $this->findOne($uuid);
        foreach ($this->form as &$f) {
            if (isset($f['attributes']) && in_array(['data-autocomplete' => 'true'], $f['attributes'])) {
                $model = '';
                $field = '';
                foreach ($f['attributes'] as $attr) {
                    foreach ($attr as $key => $value) {
                        switch ($key) {
                            case 'data-model':
                                $model = $value;
                                break;
                            case 'data-field':
                                $field = $value;
                                break;
                        }
                    }
                }
                $this->load->model($model);
                foreach ($this->$model->findIn('uuid', explode(',', $record[$f['name']])) as $option) {
                    $f['options'][] = ['text' => $option->$field, 'value' => $option->uuid];
                }
            }
            if (isset($f['multiple'])) {
                $f['value'] = explode(',', $record[$f['name']]);
            } elseif ($f['name'] === 'password') {
                $f['value'] = '';
            } else {
                $f['value'] = $record[$f['name']];
            }
        }
        return $this->form;
    }

    public function savechild($record)
    {
        $childrecords = [];
        $savedchilds  = [];

        foreach ($this->childs as $child) {
            $child_controller = $child['controller'];
            $child_model = $child['model'];
            $childrecords[$child_model] = [];
            $savedchilds[$child_model]  = [''];
            foreach ($record as $key => $value) {
                if (strpos($key, $child_controller) > -1) {
                    unset($record[$key]);
                    $childrecords[$child_model][str_replace("{$child_controller}_", '', $key)] = $value;
                }
            }
        }

        foreach ($childrecords as $childmodel => $values) {
            if (empty($values)) {
                continue;
            }
            $this->load->model($childmodel);
            $fields = array_keys($values);
            for ($index = 0; $index < count(explode(',', $childrecords[$childmodel][$fields[0]])); $index++) {
                $child_record = [];
                foreach ($fields as $field) {
                    $childinput = explode(',', $childrecords[$childmodel][$field]);
                    if (isset($childinput[$index])) {
                        $child_record[$field] = $childinput[$index];
                    }
                }
                $child_record[$this->table] = $record['uuid'];
                $savedchilds[$childmodel][] = $this->$childmodel->save($child_record);
            }
        }

        foreach ($this->childs as $child) {
            $childxmodel = $child['model'];
            $this->load->model($childxmodel);
            $childsToDelete = array_filter(
                $this->$childxmodel->find([$this->table => $record['uuid']]),
                function ($record) use ($savedchilds, $childxmodel) {
                    return !in_array($record->uuid, $savedchilds[$childxmodel]);
                }
            );
            foreach ($childsToDelete as $del) {
                $this->$childxmodel->delete($del->uuid);
            }
        }

        return $record;
    }

    /*
      fn fileupload
        input:
          - location: dir
          - newfile: $_FILES[$field_name]
          - oldfile: dir/filename.ext
        process:
        - delete old file if any
        - if newfile is null, means only delete old file
        - give unique name
        - save
        output:
        - return newfile location or empty string
    */
    public function fileupload($location, $newfile = null, $oldfile = null)
    {
        $new_file_location = '';
        $unique = time();
        if (!is_null($newfile)) {
            $extension = pathinfo($newfile['name'], PATHINFO_EXTENSION);
            $filename_without_ext = str_replace(".{$extension}", '', $newfile['name']);
            $extension = strtolower($extension);
            $new_file_location = "{$location}/{$filename_without_ext}_{$unique}.{$extension}";
            move_uploaded_file($newfile['tmp_name'], $new_file_location);
        }
        if (!is_null($oldfile) && file_exists($oldfile)) {
            unlink($oldfile);
        }
        return $new_file_location;
    }
}
