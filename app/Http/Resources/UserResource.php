<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        $data= [
            'id'              => $this->id,
            'employeeCode'    => $this->employee_code,
            'firstName'       => $this->first_name,
            'lastName'        => $this->last_name,
            'phoneNumber'     => $this->phone_number,
            'status'          => $this->status,
            'jobPosition'     => new JobPositionResource($this->jobPosition),
            'branch'          => new BranchResource($this->branch),
            'department'      => new DepartmentResource($this->department),
            'role'            => new UserRoleResource($this->role),
            'physicalAddress' => $this->physical_address,
            'dateOfBirth'     => $this->date_of_birth,
            'nationalId'      => $this->national_id,
            'gender'          => $this->gender,
            'email'           => $this->email,
            'createdAt'       => $this->created_at,
            'updatedAt'       => $this->updated_at,

        ];

        if($this->whenLoaded('driverInfo') && $this->jobPosition && $this->jobPosition->name === 'Driver'){
            $data['driverInfo'] = new DriverInfoResource($this->driverInfo); 
        }

        return $data;
    }
}
