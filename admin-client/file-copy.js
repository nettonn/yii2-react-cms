// var path = require("path");
const ncp = require("ncp").ncp;
const fsExtra = require("fs-extra");

ncp.limit = 16;

const tasks = [
  // {
  //   src: "./node_modules/bootstrap-icons/icons",
  //   dest: "./public/icons",
  // },
  {
    src: "./build",
    dest: "./../public_html/admin",
  },
];

console.log("Copying files...");

tasks.forEach((task) => {
  fsExtra.emptyDirSync(task.dest);
  console.log(`Empty dir ${task.dest}...`);

  ncp(task.src, task.dest, function (err) {
    if (err) {
      return console.error(err);
    }
  });

  console.log("Copying files complete.");
});
