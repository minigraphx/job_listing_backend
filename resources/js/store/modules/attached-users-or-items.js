import { vueFetch } from "use-lightweight-fetch";

// get users
const {
    handleData: handleGetUsers,
    fetchedData: fetchedUsers,
    isError: isErrorUsers,
    error: errorUsers,
    errors: errorsUsers,
    isLoading: isLoadingUsers,
    isSuccess: isSuccessUsers,
} = vueFetch();

export default {
    namespaced: true,

    // state
    state: {
        users: [],
        postCategories: [],
        jobCategories: [],
        storeCategories: [],
        attachedUsers: [],
        attachedPostCategories: [],
        attachedJobCategories: [],
        attachedStoreCategories: [],
        // end state
    },

    // getters
    getters: {
        getCurrentUsers(state) {
            return state.users;
        },
        getCurrentAttachedUsers(state) {
            return state.attachedUsers;
        },
        getCurrentAttachedPostCategories(state) {
            return state.attachedPostCategories;
        },
        getCurrentAttachedJobCategories(state) {
            return state.attachedJobCategories;
        },
        getCurrentAttachedStoreCategories(state) {
            return state.attachedStoreCategories;
        },
        // end getters
    },

    // mutations
    mutations: {
        setCurrentUsers(state, payload) {
            state.users = payload;
        },
        setCurrentPostCategories(state, payload) {
            state.postCategories = payload;
        },
        setCurrentJobCategories(state, payload) {
            state.jobCategories = payload;
        },
        setCurrentStoreCategories(state, payload) {
            state.storeCategories = payload;
        },

        // users
        setCurrentAttachedUsers(state, payload) {
            // check if user already exists in the array
            const userExists = state.attachedUsers.some(
                (user) => user.id === payload.id
            );

            // if user does not exist, push to array
            if (userExists === false) {
                state.attachedUsers.unshift(payload);
            }
        },
        setCurrentAttachedUsersToEmptyArray(state, payload) {
            state.attachedUsers = payload;
        },
        setRemoveAttachedUser(state, payload) {
            state.attachedUsers = payload;
        },
        // post categories
        // post categories
        // post categories
        // post categories
        setCurrentAttachedPostCategories(state, payload) {
            // check if user already exists in the array
            const itemExists = state.attachedPostCategories.some(
                (item) => item.id === payload.id
            );

            // if user does not exist, push to array
            if (itemExists === false) {
                state.attachedPostCategories.unshift(payload);
            }
        },
        setCurrentAttachedPostCategoriesToEmptyArray(state, payload) {
            state.attachedPostCategories = payload;
        },
        setRemoveAttachedPostCategories(state, payload) {
            state.attachedPostCategories = payload;
        },
        // job categories
        // job categories
        // job categories
        // job categories
        setCurrentAttachedJobCategories(state, payload) {
            // check if user already exists in the array
            const itemExists = state.attachedJobCategories.some(
                (item) => item.id === payload.id
            );

            // if user does not exist, push to array
            if (itemExists === false) {
                state.attachedJobCategories.unshift(payload);
            }
        },
        setCurrentAttachedJobCategoriesToEmptyArray(state, payload) {
            state.attachedJobCategories = payload;
        },
        setRemoveAttachedJobCategories(state, payload) {
            state.attachedJobCategories = payload;
        },
        // store categories
        // store categories
        // store categories
        // store categories
        setCurrentAttachedStoreCategories(state, payload) {
            // check if user already exists in the array
            const itemExists = state.attachedStoreCategories.some(
                (item) => item.id === payload.id
            );

            // if user does not exist, push to array
            if (itemExists === false) {
                state.attachedStoreCategories.unshift(payload);
            }
        },
        getCurrentAttachedStoreCategoriesToEmptyArray(state, payload) {
            state.attachedStoreCategories = payload;
        },
        setRemoveAttachedStoreCategories(state, payload) {
            state.attachedStoreCategories = payload;
        },

        // end mutations
    },

    // actions
    actions: {
        // get users
        async loadUsers(context, data) {
            if (data.search_query === undefined) {
                data.search_query = "";
            }
            if (data.page === undefined) {
                data.page = "";
            }

            handleGetUsers(
                route(data.apiUrlName, {
                    team: data.teamId,
                    search_query: data.search_query,
                    page: data.page,
                })
            );

            // context & send to mutation
            context.commit("setCurrentUsers", {
                fetchedData: fetchedUsers,
                isError: isErrorUsers,
                error: errorUsers,
                errors: errorsUsers,
                isLoading: isLoadingUsers,
                isSuccess: isSuccessUsers,
            });
        },
        // end action
    },
};
