<?php

namespace Home\Controller;

/**
 * [店铺数据分配]dfd.
 *
 * @author xuzan<m13265000805@163.com>
 *
 * @param  [type]    描述参数作用
 *
 * @return [type] [description]
 */
class ShopCategoryController extends CommonController
{
    public function cateAdd()
    {
        if (IS_POST) {
            $model = D('ShopCategory');
            $list = I('post.');
            $list['cate_firstid'] = I('post.firstCate');
            $list['cate_secondid'] = I('post.firstCate2');
            $list['cate_threeid'] = implode(',', I('post.firstCate3'));
            // $this->commonStore();exit;
            if ($model->create($list, 1)) {
                if ($id = $model->add()) {
                    $this->success('温馨提示：店铺需要两个工作审核，请耐心等待！', U('home/service/publishServiceOne'), 3);
                }
            }
            $this->error($model->getError());
        }
    }

    public function cateLst()
    {
        $model = D('Home/ShopCategory');
        $data = $model->getTree();
        $this->assign(
        ['data' => $data]
        );
        if (IS_POST) {
            $model = D('Home/ShopCategory');
            $a = I('post.');
            if ($a['parent_id'] == 0) {
                $a['cate_path'] = '0,';
            } else {
                $res = $model->field('cate_path')->find($a['parent_id']);
                $res = $res['cate_path'].$a['parent_id'].',';
                $a['cate_path'] = $res;
            }
            if ($model->create($a, 1)) {
                if ($id = $model->add()) {
                    $this->success('添加成功!', U('cateLst'));
                    exit;
                }
            } else {
                $this->error($model->getError());
            }
        }
        $parentModel = D('Home/ShopCategory');
        // 返回二维遍历数组
        $parentData = $parentModel->getTree();
        $this->assign(array('parentData' => $parentData));
        $this->display();
    }

    /*编辑分类*/
    public function cateEdit()
    {
        // 接收编辑的ID
        $id = I('get.id');
        if (IS_POST) {
            $model = D('Home/ShopCategory');
            if ($model->create(I('post.'), 2)) {
                if ($model->save() !== false) {
                    $this->success('修改成功！', U('cateLst', array('p' => I('get.p', 1))));
                    exit;
                }
            }
            $this->error($model->getError());
        }
        $model = M('ShopCategory');
        $data = $model->find($id);
        $this->assign('data', $data);
        $parentModel = D('Home/ShopCategory');
        $parentData = $parentModel->getTree();
            /*$parentData返回一个二维数组*/
            $children = $parentModel->getChildren($id);
            /*$children返回子类的ID*/
            $this->assign(array(
            'parentData' => $parentData,
            'children' => $children,
            ));
        $this->display();
    }

        /*删除分类*/
    public function cateDelete()
    {
        $model = D('ShopCategory');
        $map['parent_id'] = ['eq', I('get.id')];
        $res = $model->where($map)->select();
        if (!$res) {
            if ($model->delete(I('get.id', 0)) !== false) {
                $this->ajaxReturn(1);
            } else {
                $this->ajaxReturn(0);
            }
        } else {
            $this->ajaxReturn(2);
        }
    }
}
