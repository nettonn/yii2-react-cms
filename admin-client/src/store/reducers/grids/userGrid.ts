import createGridSlice from "./createGridSlice";

export const userGridSlice = createGridSlice("userGrid");

export const userGridReducer = userGridSlice.reducer;

export const userGridActions = userGridSlice.actions;
