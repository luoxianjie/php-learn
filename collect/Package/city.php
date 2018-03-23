<?php

require './vendor/autoload.php';

if(isset($_POST['parent_id'])):
    $db = Db::getInstance();
    $res = $db->table('test_city')->where(['parent_id'=>intval($_POST['parent_id'])])->select();
    echo json_encode(['status'=>200,'data'=>$res]);
else:
?>
    <html>
    <head>
        <title>测试</title>
        <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.bootcss.com/vue/2.5.16/vue.min.js"></script>
        <style>
            [v-cloak] {
                display: none;
            }
        </style>
    </head>
    <body>
        <div id="el" v-cloak>
            <select name="province" v-model="provinceId" @change="getCity"><option v-for="province,k in provinces" v-bind:value="province.id">{{province.name}}</option></select>
            <select name="city" v-model="cityId" @change="getArea"><option v-for="city in cities" :value="city.id">{{city.name}}</option></select>
            <select name="area" v-model="areaId"><option v-for="area in areas" :value="area.id">{{area.name}}</option></select>
            {{address}}
        </div>
        <script type="text/javascript">
            var vm = new Vue({
                el: '#el',
                data: {
                    provinceId:0,
                    provinces:[{id:0,name:"请选择省份"}],
                    cityId:0,
                    cities:[{id:0,name:"请选择城市"}],
                    areaId:0,
                    areas:[{id:0,name:"请选择区县"}]
                },
                computed:{
                    address:function(){
                        var address = '';
                        $.each(vm.provinces,function(k,v){
                            if(v.id == vm.provinceId && v.id!=0) address += v.name;
                        });
                        $.each(vm.cities,function(k,v){
                            if(v.id == vm.cityId && v.id!=0) address += v.name;
                        });
                        $.each(vm.areas,function(k,v){
                            if(v.id == vm.areaId && v.id!=0) address += v.name;
                        });

                        return address;
                    }
                },
                mounted: function(){
                    $.post('/',{parent_id:0},function(rsp){
                        if(rsp.status = 200){
                            vm.provinceId = 0;
                            vm.provinces = [{id:0,name:"请选择省份"}];
                            $.each(rsp.data,function(k,v){
                                vm.provinces.push(v);
                            });
                        }
                    },'json')
                },
                methods:{
                    getCity : function(){
                        $.post('/',{parent_id:vm.provinceId},function(rsp){
                            if(rsp.status = 200){
                                vm.cityId = 0;
                                vm.areaId = 0;
                                vm.cities = [{id:0,name:"请选择城市"}];
                                vm.areas = [{id:0,name:"请选择区县"}];
                                $.each(rsp.data,function(k,v){
                                    vm.cities.push(v);
                                });
                            }
                        },'json')
                    },
                    getArea : function(){
                        $.post('/',{parent_id:vm.cityId},function(rsp){
                            if(rsp.status = 200){
                                vm.areaId = 0;
                                vm.areas = [{id:0,name:"请选择区县"}];
                                $.each(rsp.data,function(k,v){
                                    vm.areas.push(v);
                                });
                            }
                        },'json')
                    }
                }
            })

        </script>
    </body>
    </html>
<?php
endif;
?>
