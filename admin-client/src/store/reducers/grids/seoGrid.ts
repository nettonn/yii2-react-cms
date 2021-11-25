import createGridSlice from "./createGridSlice";

export const seoGridSlice = createGridSlice("seoGrid");

export const seoGridReducer = seoGridSlice.reducer;

export const seoGridActions = seoGridSlice.actions;
