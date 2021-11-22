import createGridSlice from "./createGridSlice";

export const pageGridSlice = createGridSlice("pageGrid");

export const pageGridReducer = pageGridSlice.reducer;

export const pageGridActions = pageGridSlice.actions;
