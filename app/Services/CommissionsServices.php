<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 15-04-2018
 * Time: 19:00
 */

namespace App\Services;

Use App\Models\Commission;
use App\Models\MensuelleCommission;
use App\Models\VirementExterne;
use App\Models\VirementInterne;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CommissionsServices
{

    /**
     * Get commissions's list
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function findAll(){
        $commissions = Commission::all();
        return $commissions;
    }

   

    /**
     * Find a commission by id
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
     public function findById($id){
        $commission = Commission::where('id',$id)->first();
        return $commission;
    }


    /**
     * Create a new commission
     * @param $data
     */
    public function create($data){
        $commission = new Commission();
        $commission->description = strip_tags($data['description']);
        $commission->id = strip_tags($data['code']);
        $commission->type = strip_tags($data['type']);
        $commission->valeur = strip_tags($data['valeur']);
        $commission->save();
    }


    /**
     * Update a commission's data
     * @param $commission
     * @param $data
     */
    public function update($commission,$data){
        $commission->update(['description'=> $data['description'], 'id'=> $data['code']
            , 'type'=> $data['type'], 'valeur'=> $data['valeur']]);
    }


    /**
     * Delete a commission
     * @param $commission
     * @param $id
     */
    public function delete($commission,$id){
        $commission->delete();
    }

    //--------------------------------------  comm stat  ---------------------------------------//

    public function getCommissionStat(){
//        try{
            $data = array();
            // Per Quarter
            $dataQ = $this->getCommissionPerQuarter();
            $data['quarter'] = $dataQ;

            // Per Mounth
            $dataM = $this->getCommissionPerMonth();
            $data['month'] = $dataM;

            // Per Year
             $dataY = $this->getCommissionPerYear(3);
            $data['year'] = $dataY;

            return $data;
//        }catch (\Exception $e){
//            return null;
//        }
    }


    /**
     * @return array
     */
    public function getCommissionPerQuarter(){
        $chartDatas1 = VirementInterne::select([
            DB::raw('QUARTER(created_at) AS quarter'),
            DB::raw('SUM(montant_commission) AS sum'),
        ])
            ->whereBetween('created_at', [Carbon::now()->subQuarter(4), Carbon::now()])
            ->groupBy('quarter')
            ->orderBy('quarter', 'ASC')
            ->get()
            ->toArray();

        $chartDatas2 =VirementExterne::select([
                DB::raw('QUARTER(created_at) AS quarter'),
                DB::raw('SUM(amount_commission) AS sum'),
            ]
        )
            ->whereBetween('created_at', [Carbon::now()->subQuarter(4), Carbon::now()])
            ->groupBy('quarter')
            ->orderBy('quarter', 'ASC')
            ->get()
            ->toArray();

        $commMens = MensuelleCommission::select([
                DB::raw('QUARTER(created_at) AS quarter'),
                DB::raw('SUM(amount) AS sum'),
            ]
            )->whereBetween('created_at', [Carbon::now()->subQuarter(4), Carbon::now()])
            ->groupBy('quarter')
            ->orderBy('quarter', 'ASC')
            ->get()
            ->toArray();

        // menseulles
        $date = new Carbon;
        $chartDataMens= array();
        foreach ($commMens as $data) {
            $chartDataMens[$data['quarter']] = $data['sum'];
        }
        for ($i = 0; $i < 4; $i++) {
            $dateString = $date->quarter;
            if (!isset($chartDataMens[$dateString])) {
                $chartDataMens[$dateString] = 0;
            }
            $date->subQuarter();
        }


        $chartDataByQ = array();
        foreach($chartDatas1 as $data) {
            $chartDataByQ[$data['quarter']] = $data['sum'];
        }

        $date = new Carbon;
        for($i = 0; $i < 4; $i++) {
            $dateString = $date->quarter;
            if(!isset($chartDataByQ[ $dateString ])){
                $chartDataByQ[ $dateString ] = 0;
            }
            $date->subQuarter();
        }

        $chartDataByQ2 = array();
        foreach($chartDatas2 as $data) {
            $chartDataByQ2[$data['quarter']] = $data['sum'];
        }

        $date = new Carbon;
        for($i = 0; $i < 4; $i++) {
            $dateString = $date->quarter;
            if(!isset($chartDataByQ2[ $dateString ])){
                $chartDataByQ2[ $dateString ] = 0;
            }
            $date->subQuarter();
        }

        $date = new Carbon;
        for ($i = 1; $i < 5; $i++) {
            if (!isset($chartDataByQ2[$i ])) {
                $chartDataByQ2[$i] = $chartDataByQ[$i]+$chartDataMens[$i];
            }else{
                $chartDataByQ2[$i] += $chartDataByQ[$i]+$chartDataMens[$i];
            }
            $date->subQuarter();
        }


        return $chartDataByQ2;
    }

    /**
     * @return array|null
     */
    public function getCommissionPerMonth(){
        $chartDatas1 = VirementInterne::select([
            DB::raw('MONTH(created_at) AS month'),
            DB::raw('SUM(montant_commission) AS sum'),
        ])
            ->whereBetween('created_at', [Carbon::now()->subMonth(12), Carbon::now()])
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get()
            ->toArray();

        $chartDatas2 =VirementExterne::select([
                DB::raw('MONTH(created_at) AS month'),
                DB::raw('SUM(amount_commission) AS sum'),
            ]
        )
            ->whereBetween('created_at', [Carbon::now()->subMonth(12), Carbon::now()])
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get()
            ->toArray();

        $commMens = MensuelleCommission::select([
                DB::raw('MONTH(created_at) AS month'),
                DB::raw('SUM(amount) AS sum'),
            ]
        )->whereBetween('created_at', [Carbon::now()->subMonth(12), Carbon::now()])
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get()
            ->toArray();

        // menseulles
        $date = new Carbon;
        $chartDataMens= array();
        foreach ($commMens as $data) {
            $chartDataMens[$data['month']] = $data['sum'];
        }
        for ($i = 0; $i < 12; $i++) {
            $dateString = $date->month;
            if (!isset($chartDataMens[$dateString])) {
                $chartDataMens[$dateString] = 0;
            }
            $date->subMonth();
        }

        $chartDataByQ = array();
        foreach($chartDatas1 as $data) {
            $chartDataByQ[$data['month']] = $data['sum'];
        }

        $date = new Carbon;
        for($i = 0; $i < 12; $i++) {
            $dateString = $date->month;
            if(!isset($chartDataByQ[ $dateString ])){
                $chartDataByQ[ $dateString ] = 0;
            }
            $date->subMonth();
        }

        $chartDataByQ2 = array();
        foreach($chartDatas2 as $data) {
            $chartDataByQ2[$data['month']] = $data['sum'];
        }

        $date = new Carbon;
        for($i = 0; $i < 12; $i++) {
            $dateString = $date->month;
            if(!isset($chartDataByQ2[ $dateString ])){
                $chartDataByQ2[ $dateString ] = 0;
            }
            $date->subMonth();
        }

        $date = new Carbon;
        for ($i = 1; $i < 13; $i++) {
            if (!isset($chartDataByQ2[$i])) {
                $chartDataByQ2[$i] = $chartDataByQ[$i]+$chartDataMens[$i];
            }else{
                $chartDataByQ2[$i] += $chartDataByQ[$i]+$chartDataMens[$i];
            }
            $date->subMonth();
        }

        return $chartDataByQ2;
    }

    /**
     * @param $nbYear
     * @return array|null
     */
    public function getCommissionPerYear($nbYear)
    {
        $chartDatas1 = VirementInterne::select([
            DB::raw('YEAR(created_at) AS year'),
            DB::raw('SUM(montant_commission) AS sum'),
        ])
            ->whereBetween('created_at', [Carbon::now()->subYear($nbYear), Carbon::now()])
            ->groupBy('year')
            ->orderBy('year', 'ASC')
            ->get()
            ->toArray();

        $chartDatas2 = VirementExterne::select([
                DB::raw('YEAR(created_at) AS year'),
                DB::raw('SUM(amount_commission) AS sum'),
            ]
        )
            ->whereBetween('created_at', [Carbon::now()->subYear($nbYear), Carbon::now()])
            ->groupBy('year')
            ->orderBy('year', 'ASC')
            ->get()
            ->toArray();

        $commMens = MensuelleCommission::select([
                DB::raw('YEAR(created_at) AS year'),
                DB::raw('SUM(amount) AS sum'),
            ]
        )
            ->whereBetween('created_at', [Carbon::now()->subYear($nbYear), Carbon::now()])
            ->groupBy('year')
            ->orderBy('year', 'ASC')
            ->get()
            ->toArray();

        // menseulles
        $date = new Carbon;
        $chartDataMens= array();
        foreach ($commMens as $data) {
            $chartDataMens[$data['year']] = $data['sum'];
        }
        for ($i = 0; $i < $nbYear; $i++) {
            $dateString = $date->year;
            if (!isset($chartDataMens[$dateString])) {
                $chartDataMens[$dateString] = 0;
            }
            $date->subYear();
        }


        // internes
        $chartDataByQ = array();
        foreach ($chartDatas1 as $data) {
            $chartDataByQ[$data['year']] = $data['sum'];
        }

        $date = new Carbon;
        for ($i = 0; $i < $nbYear; $i++) {
            $dateString = $date->year;
            if (!isset($chartDataByQ[$dateString])) {
                $chartDataByQ[$dateString] = 0;
            }
            $date->subYear();
        }


        // extrenes with others
        $chartDataByQ2 = array();
        foreach ($chartDatas2 as $data) {
            $chartDataByQ2[$data['year']] = $data['sum'];
        }

        $date = new Carbon;
        for ($i = 0; $i < $nbYear; $i++) {
            $dateString = $date->year;
            if (!isset($chartDataByQ2[$dateString])) {
                $chartDataByQ2[$dateString] = $chartDataByQ[$dateString]+$chartDataMens[$dateString];
            }else{
                $chartDataByQ2[$dateString] += $chartDataByQ[$dateString]+$chartDataMens[$dateString];
            }
            $date->subYear();
        }


        return $chartDataByQ2;
    }

}