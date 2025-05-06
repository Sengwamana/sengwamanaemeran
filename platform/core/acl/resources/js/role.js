class Role {
    init() {

        let $checkboxes = $('.has-children')
        if ($checkboxes.length) {
            $checkboxes.map((index, value) => {
                $(value).treeview({
                    collapsed: true,
                    animated: "medium",
                    control:"#sidetreecontrol",
                    persist: "location"
                });
            })
        }

        $('#allTreeChecked:checkbox').on('click', function (event){
            event.stopPropagation();
            let _self = $(event.currentTarget);
            let checked = _self.is(':checked');
            if($('#checkboxes-permisstions').length){
                var parent_uls = $('#checkboxes-permisstions').find(':checkbox').prop('checked', checked);
                parent_uls.each(function(){
                    var parent_ul = $(this),
                    parent_state = (parent_ul.find(':checkbox').length == parent_ul.find(':checked').length); 
                    parent_ul.siblings(':checkbox').prop('checked', parent_state);
                });
            }
            
         });

         $('#checkboxes-permisstions :checkbox').on('click', function (event){
            event.stopPropagation();
            let _self = $(event.currentTarget);
            let checked = _self.is(':checked'),
            parent_li = _self.closest('li'),
            parent_uls = parent_li.parents('ul');
            parent_li.find(':checkbox').prop('checked', checked);
            parent_uls.each(function(){
                let parent_ul = $(this),
                parent_state = (parent_ul.find(':checkbox').length == parent_ul.find(':checked').length); 
                parent_ul.siblings(':checkbox').prop('checked', parent_state);
            });
         });
    }
}

$(() => {
    new Role().init()
})
