<template>
    <div class="admin__field field">
        <auto-complete
            :label="config.translation.categoryChild"
            :description="config.translation.categoryId"
            :item="item"
            :options="config.fieldData.category_child.snowMenuAutoCompleteField.options"
            :config="config"
        />
    </div>
</template>
<script>
    define(["Vue"], function(Vue) {
        Vue.component("category_child", {
            props: ['config', 'item'],
            template: template
        });
    });
</script>
