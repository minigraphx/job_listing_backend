<script setup>
import Modal from "@/Components/Modals/Modal.vue";
import { ref, computed, onMounted, onBeforeMount, nextTick } from "vue";
import { TailwindPagination } from "laravel-vue-pagination";
import componentHelpers from "@/utils/builder/html-elements/componentHelpers";
import ThumbnailSmallImageSlider from "@/Components/ImageSliders/ThumbnailSmallImageSlider.vue";
import PageBuilder from "@/composables/PageBuilder";
import SmallUniversalSpinner from "@/Components/Loaders/SmallUniversalSpinner.vue";

import { useStore } from "vuex";

const props = defineProps({
    team: {
        required: true,
    },
    firstButtonText: {
        required: true,
    },
    title: {
        required: true,
    },
    show: {
        type: Boolean,
        default: false,
        required: true,
    },
});

const emit = defineEmits(["firstModalButtonSearchComponentsFunction"]);

// first button function
const firstButton = function () {
    emit("firstModalButtonSearchComponentsFunction");
};

const search_query = ref("");
const categorySelected = ref(
    { name: "All", id: null },
    { name: "HTML Elements", id: null }
);
const store = useStore();
const pageBuilder = new PageBuilder(store);

// use dynamic model
const getComponents = computed(() => {
    return store.getters["pageBuilderState/getComponents"];
});
const getFetchedComponents = computed(() => {
    return store.getters["pageBuilderState/getFetchedComponents"];
});

const getComponentArrayAddMethod = computed(() => {
    return store.getters["pageBuilderState/getComponentArrayAddMethod"];
});

const handleDropComponent = async function (componentObject) {
    await nextTick();
    const clonedComponent = pageBuilder.cloneCompObjForDOMInsertion({
        html_code: componentObject.html_code,
        id: componentObject.id,
    });

    await nextTick();

    store.commit("pageBuilderState/setPushComponents", {
        component: clonedComponent,
        componentArrayAddMethod: getComponentArrayAddMethod.value,
    });

    await nextTick();
    pageBuilder.setEventListenersForElements();

    // Close modal
    firstButton();
};

const handleAddHelperComponent = async function (helperComponentObject) {
    await nextTick();
    const clonedComponent = pageBuilder.cloneCompObjForDOMInsertion({
        html_code: helperComponentObject.html_code,
        id: helperComponentObject.id,
    });

    await nextTick();
    store.commit("pageBuilderState/setPushComponents", {
        component: clonedComponent,
        componentArrayAddMethod: getComponentArrayAddMethod.value,
    });

    await nextTick();
    pageBuilder.setEventListenersForElements();

    // Close modal
    firstButton();
};

//
//
//
// handle search
const handleSearch = function (page) {
    // dispatch
    store.dispatch("pageBuilderState/loadComponents", {
        page: page,
        search_query: search_query.value,
        category: categorySelected.value,
    });
};

const fetchComponents = function (page) {
    //remember old search value while paginating
    if (getFetchedComponents.value?.fetchedData?.oldInput?.search_query) {
        search_query.value =
            getFetchedComponents.value?.fetchedData?.oldInput?.search_query;
    }
    //
    // dispatch
    store.dispatch("pageBuilderState/loadComponents", {
        page: page,
        search_query: search_query.value,
        category: categorySelected.value,
    });
};

// get result for "laravel pagination" package
const getResultsForPage = (page = 1) => {
    fetchComponents(page);
};

const handleCategory = function (category) {
    categorySelected.value = category;

    // dispatch
    store.dispatch("pageBuilderState/loadComponents", {
        page: 1,
        search_query: search_query.value,
        category: category,
    });
};
//
onMounted(async () => {
    fetchComponents(1);
});
</script>

<template>
    <Modal
        maxWidth="5xl"
        :show="show"
        @close="firstButton"
        minHeight=""
        maxHeight=""
    >
        <div
            class="w-full relative inline-block align-bottom text-left overflow-hidden transform transition-all sm:align-middle"
        >
            <div class="flex items-center border-b border-gray-200 p-4 mb-2">
                <div class="flex-1">
                    <h3 class="tertiaryHeader my-0 py-0">
                        {{ title }}
                    </h3>
                </div>
                <div
                    class="h-10 w-10 flex-start cursor-pointer rounded-full flex items-center border-none justify-center bg-gray-50 aspect-square hover:bg-myPrimaryLinkColor hover:text-white hover:fill-white focus-visible:ring-0"
                    @click="firstButton"
                >
                    <span class="material-symbols-outlined"> close </span>
                </div>
            </div>

            <div class="p-4">
                <!-- content start -->
                <template
                    v-if="
                        !getFetchedComponents.isLoading &&
                        getFetchedComponents.isError
                    "
                >
                    <p class="myPrimaryInputError">
                        {{ getFetchedComponents.error }}
                    </p>
                </template>

                <!--  -->

                <!-- Loading # start -->
                <template v-if="getFetchedComponents.isLoading">
                    <SmallUniversalSpinner
                        class="h-40"
                        width="w-6"
                        height="h-6"
                        border="border-4"
                    ></SmallUniversalSpinner>
                </template>

                <!-- Actual data # start -->
                <template
                    v-if="
                        !getFetchedComponents.isError &&
                        !getFetchedComponents.isLoading
                    "
                >
                    <!-- Search # Start -->
                    <form @submit.prevent="handleSearch(1)">
                        <div class="mysearchBarWithOptions">
                            <div class="relative w-full">
                                <div
                                    class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none"
                                >
                                    <span class="material-symbols-outlined">
                                        search
                                    </span>
                                </div>
                                <input
                                    type="search"
                                    id="search_query"
                                    v-model="search_query"
                                    class="myPrimarySearchInput"
                                    autocomplete="off"
                                    placeholder="Search..."
                                />
                            </div>

                            <button
                                @click="handleSearch(1)"
                                type="button"
                                class="myPrimarySearchButton"
                            >
                                Search
                            </button>
                        </div>
                    </form>
                    <!-- Search # End -->

                    <!-- Categories # Start -->
                    <div class="flex gap-2 flex-wrap">
                        <button
                            @click="handleCategory({ name: 'All', id: null })"
                            class="myPrimaryTag font-medium"
                            :class="[
                                {
                                    'bg-myPrimaryLinkColor text-white':
                                        categorySelected.name === 'All',
                                },
                            ]"
                            :disabled="categorySelected.name === 'All'"
                        >
                            All
                        </button>
                        <button
                            @click="
                                handleCategory({
                                    name: 'HTML Elements',
                                    id: null,
                                })
                            "
                            class="myPrimaryTag font-medium"
                            :class="[
                                {
                                    'bg-myPrimaryLinkColor text-white':
                                        categorySelected.name ===
                                        'HTML Elements',
                                },
                            ]"
                            :disabled="
                                categorySelected.name === 'HTML Elements'
                            "
                        >
                            HTML Elements
                        </button>

                        <template
                            v-for="category in getFetchedComponents &&
                            getFetchedComponents.fetchedData &&
                            Array.isArray(
                                getFetchedComponents.fetchedData
                                    .component_categories
                            ) &&
                            getFetchedComponents.fetchedData
                                .component_categories"
                            :key="category.id"
                        >
                            <button
                                @click="
                                    handleCategory({
                                        name: category.name,
                                        id: category.id,
                                    })
                                "
                                class="myPrimaryTag font-medium"
                                :class="[
                                    {
                                        'bg-myPrimaryLinkColor text-white':
                                            categorySelected.name ===
                                            category.name,
                                    },
                                    {
                                        'bg-myPrimaryLinkColor text-white':
                                            categorySelected.name ===
                                            category.name,
                                    },
                                ]"
                                :disabled="
                                    categorySelected.name === category.name
                                "
                            >
                                {{ category.name }}
                            </button>
                        </template>
                    </div>
                    <!-- Categories # End -->
                    <div
                        v-if="
                            categorySelected &&
                            categorySelected.name !== 'HTML Elements' &&
                            getFetchedComponents &&
                            getFetchedComponents.fetchedData &&
                            getFetchedComponents.fetchedData.components
                        "
                        class="flex items-center justify-center border-t border-gray-200 bg-white py-3 mt-4 gap-2"
                    >
                        <TailwindPagination
                            :limit="1"
                            :keepLength="true"
                            :class="[
                                'space-x-1',
                                'shadow-none',
                                'tailwind-pagination-package',
                            ]"
                            :active-classes="[
                                'bg-myPrimaryLinkColor',
                                'text-white',
                                'rounded-full',
                            ]"
                            :item-classes="[
                                'p-0',
                                'm-0',
                                'border-none',
                                'bg-myPrimaryLightGrayColor',
                                'shadow-sm',
                                'hover:bg-gray-300',
                                'text-myPrimaryDarkGrayColor',
                                'rounded-full',
                            ]"
                            :data="getFetchedComponents.fetchedData.components"
                            @pagination-change-page="getResultsForPage"
                        >
                            <template #prev-nav>
                                <span> Prev </span>
                            </template>
                            <template #next-nav>
                                <span>Next</span>
                            </template>
                        </TailwindPagination>
                    </div>

                    <div
                        class="h-full flex md:flex-row flex-col myPrimaryGap mt-2 p-2 overflow-y-scroll"
                    >
                        <section class="md:w-4/6">
                            <template
                                v-if="
                                    categorySelected &&
                                    categorySelected.name !== 'HTML Elements'
                                "
                            >
                                <div
                                    class="overflow-scroll min-h-[25rem] max-h-[25rem] grid myPrimaryGap md:grid-cols-2 grid-cols-2 w-full myPrimaryGap px-2 p-4 border border-myPrimaryLightGrayColor rounded-lg"
                                >
                                    <div
                                        class="overflow-hidden whitespace-pre-line flex-1 h-auto rounded border border-gray-300 lg:py-10 py-8 px-2"
                                        v-for="component in getFetchedComponents &&
                                        getFetchedComponents.fetchedData &&
                                        getFetchedComponents.fetchedData
                                            .components &&
                                        Array.isArray(
                                            getFetchedComponents.fetchedData
                                                .components.data
                                        ) &&
                                        getFetchedComponents.fetchedData
                                            .components.data"
                                        :key="component.id"
                                    >
                                        <div class="relative">
                                            <template
                                                v-if="
                                                    component &&
                                                    component.cover_images
                                                "
                                            >
                                                <ThumbnailSmallImageSlider
                                                    :images="
                                                        component.cover_images
                                                    "
                                                    imageSize="medium_path"
                                                    imageHeight="max-h-72"
                                                    imageWidth="w-full cursor-pointer object-contain bg-white"
                                                    :roundedFull="false"
                                                    :squareButtons="true"
                                                    @firstButtonClick="
                                                        handleDropComponent(
                                                            component
                                                        )
                                                    "
                                                    :imageClickable="true"
                                                ></ThumbnailSmallImageSlider>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <!-- if category selected is HTML Elements -->
                            <template
                                v-if="
                                    categorySelected &&
                                    categorySelected.name === 'HTML Elements'
                                "
                            >
                                <div
                                    class="min-h-[30rem] max-h-[30rem] w-full border rounded-lg py-4 px-2 overflow-scroll"
                                >
                                    <div class="flex gap-4 flex-wrap">
                                        <!-- Unique HTML Component # start -->
                                        <div
                                            class="flex justify-left items-center gap-4 text-sm font-medium"
                                            v-for="helperComponent in componentHelpers"
                                            :key="helperComponent.title"
                                        >
                                            <button
                                                @click="
                                                    handleAddHelperComponent(
                                                        helperComponent
                                                    )
                                                "
                                                type="button"
                                                class="mySecondaryButton px-6 py-4"
                                            >
                                                <span
                                                    class="material-symbols-outlined text-sm"
                                                >
                                                    add
                                                </span>
                                                <span>
                                                    {{ helperComponent.title }}
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <!-- if category selected is HTML Elements -->
                        </section>
                        <aside
                            class="md:w-2/6 overflow-scroll min-h-[30rem] max-h-[30rem] w-full border rounded-lg py-4 px-2"
                        >
                            <div class="flex gap-4 flex-wrap w-full">
                                <!-- Unique HTML Component # start -->
                                <div
                                    v-for="helperComponent in componentHelpers"
                                    :key="helperComponent.title"
                                >
                                    <div
                                        class="flex justify-left items-center gap-4 text-xs font-medium"
                                    >
                                        <button
                                            @click="
                                                handleAddHelperComponent(
                                                    helperComponent
                                                )
                                            "
                                            type="button"
                                            class="mySecondaryButton"
                                        >
                                            <span
                                                class="material-symbols-outlined text-sm"
                                            >
                                                add
                                            </span>
                                            <span>
                                                {{ helperComponent.title }}
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </template>
                <!-- Actual data # start -->
            </div>
        </div>
        <div
            v-if="!getFetchedComponents.isError"
            class="bg-slate-50 px-2 py-4 flex sm:justify-end justify-center"
        >
            <div class="sm:w-3/6 w-full px-2 my-2 flex gap-2 justify-end">
                <button
                    v-if="firstButtonText"
                    ref="firstButtonRef"
                    class="mySecondaryButton"
                    type="button"
                    @click="firstButton"
                >
                    {{ firstButtonText }}
                </button>
            </div>
        </div>
    </Modal>
</template>
