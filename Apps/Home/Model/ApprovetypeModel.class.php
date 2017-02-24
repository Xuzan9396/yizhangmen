<?php
    namespace Home\Model;

    use Think\Model;

    class ApprovetypeModel extends Model
    {
        /**
         * 获取认证类型信息
         * @author YangJun
         * @return array 返回认证类型信息
         */
        public function getApprovetype()
        {
            $data = $this->field('appe_id,appe_type')->select();
            return $data;
        }

        /**
         * 获取认证类型id
         * @author YangJun
         * @return array 返回认证类型id
         */
        public function getOneApprovetype()
        {
            $data = $this->field('appe_id')->select();
            return $data;
        }

        /**
         * 获取一条认证类型信息
         * @author YangJun
         * @return array 返回认证类型信息
         */
        public function getApprovetypeInfo($appe_id)
        {
            $where['appe_id'] = ['eq' , $appe_id];
            $data = $this->where($where)->find();
            return $data;
        }
        
    }
