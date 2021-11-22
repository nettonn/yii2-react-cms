import createGridSlice from "./createGridSlice";

export const postGridSlice = createGridSlice("postGrid");

export const postGridReducer = postGridSlice.reducer;

export const postGridActions = postGridSlice.actions;
