<!DOCTYPE html>
<html>
<head>
    <title>test material calculation</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>    
</head>
<body>
    <div class="container" id="crudApp">
        <br />

        <h1 align="center">TEST MATERIAL THICKNESS CATCULATION</h1>


        <select name='specialShape' id='specialShape' @change='getMaterial()' v-model='specialShape'>
            <option value='#' disabled>Please select one... </option>
            <option value='NORMAL'>Normal Shape</option>
            <option value='PLATEC'>Circular Cut Plate</option>
            <option value='PLATECO'>Circular Ring Plate</option>
        </select>
        <select name='mat' id='mat' @change='getThickList()' v-model='matcode'>
            <option v-for='option in materials' v-bind:value='option.materialcode'>{{option.material}}</option>
        </select>
        <br>
        <input id='dT' name='dT' v-model='thick' list="thickList" value="" placeholder="dT" @change='getW1List()' />
        <datalist name='thickList' id='thickList'>
            <option v-for='option in thickList' v-bind:value='option'>{{option}}</option>
        </datalist>
        <input id='dW1' name='dW1' v-model='W1' list="W1List" value="" placeholder="dW1" @change='getW2List()'/>
        <datalist name='W1List' id='W1List'>
            <option v-for='option in W1List' v-bind:value='option'>{{option}}</option>
        </datalist>
        <input id='dW2' name='dW2' v-model='W2' list="W2List" value="" placeholder="dW2" />
        <datalist name='W2List' id='W2List'>
            <option v-for='option in W2List' v-bind:value='option'>{{option}}</option>
        </datalist>
    </div>
</body>
</html>

<script>
const app = new Vue({
    el: '#app',
    data: function () {
        return{
            show: true
        }
    }
});


var application = new Vue({
    el: '#crudApp',
    data: {
        allData: '',
        myModel: false,
        actionButton: 'Insert',
        dynamicTitle: 'Add Data',
        specialShape: '#',
        materials: '',
        matcode: '',
        thick: '',
        thickList: '',
        W1: '',
        W1List: '',
        W2: '',
        W2List: ''
    },
    methods: {
        getMaterial: function () {
            //let specialShape = document.getElementById('specialShape').value;
            //console.log(specialShape);
            axios.post('action2.php', {
                action: 'getMaterial',
                specialShape: application.specialShape
            }).then(function (response) {
                application.materials = response.data;
            });
        },
        getThickList: function () {
            let matdata = document.getElementById('mat').value;
            console.log(matdata);
            console.log(application.matcode);
            axios.post('action2.php', {
                action: 'getThickList',
                matcode: application.matcode
            }).then(function (response) {
                application.thickList = response.data;
            });
        },
        getW1List: function () {
            console.log("In getW1List");
            console.log(application.matcode);
            console.log("Thick = " + application.thick);
            axios.post('action2.php', {
                action: 'getW1List',
                matcode: application.matcode,
                thick: application.thick
            }).then(function (response) {
                application.W1List = response.data;
            });
            console.log('After process : ' + application.W1List);
        },
        getW2List: function () {
            console.log(application.matcode);
            axios.post('action2.php', {
                action: 'getW2List',
                matcode: application.matcode,
                thick: application.thick,
                W1: application.W1
            }).then(function (response) {
                application.W2List = response.data;
            });
        },
        fetchAllData: function () {
            axios.post('action.php', {
                action: 'fetchall'
            }).then(function (response) {
                application.allData = response.data;
            });
        },
        openModel: function () {
            application.first_name = '';
            application.last_name = '';
            application.actionButton = "Insert";
            application.dynamicTitle = "Add Data";
            application.myModel = true;
        },
        submitData: function () {
            if (application.first_name != '' && application.last_name != '')
            {
                if (application.actionButton == 'Insert')
                {
                    axios.post('action.php', {
                        action: 'insert',
                        firstName: application.first_name,
                        lastName: application.last_name
                    }).then(function (response) {
                        application.myModel = false;
                        application.fetchAllData();
                        application.first_name = '';
                        application.last_name = '';
                        alert(response.data.message);
                    });
                }
                if (application.actionButton == 'Update')
                {
                    axios.post('action.php', {
                        action: 'update',
                        firstName: application.first_name,
                        lastName: application.last_name,
                        hiddenId: application.hiddenId
                    }).then(function (response) {
                        application.myModel = false;
                        application.fetchAllData();
                        application.first_name = '';
                        application.last_name = '';
                        application.hiddenId = '';
                        alert(response.data.message);
                    });
                }
            } else
            {
                alert("Fill All Field");
            }
        },
        fetchData: function (id) {
            axios.post('action.php', {
                action: 'fetchSingle',
                id: id
            }).then(function (response) {
                application.first_name = response.data.first_name;
                application.last_name = response.data.last_name;
                application.hiddenId = response.data.id;
                application.myModel = true;
                application.actionButton = 'Update';
                application.dynamicTitle = 'Edit Data';
            });
        },
        deleteData: function (id) {
            if (confirm("Are you sure you want to remove this data?"))
            {
                axios.post('action.php', {
                    action: 'delete',
                    id: id
                }).then(function (response) {
                    application.fetchAllData();
                    alert(response.data.message);
                });
            }
        }
    },
    created: function () {
        this.fetchAllData();
        this.getMaterial();
    }
});

</script>
