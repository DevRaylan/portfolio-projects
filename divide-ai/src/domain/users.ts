export type AppUser = {
  id: string;
  name: string;
  email: string;
  role: "morador";
};

export const demoUsersVersion = "initial-users-v1";

export const demoUsers: AppUser[] = [
  { id: "user-beatriz", name: "Beatriz Costa", email: "beatriz.costa@divideai.demo", role: "morador" },
  { id: "user-bruno", name: "Bruno Lima", email: "bruno.lima@divideai.demo", role: "morador" },
  { id: "user-camila", name: "Camila Souza", email: "camila.souza@divideai.demo", role: "morador" },
  { id: "user-rafael", name: "Rafael Martins", email: "rafael.martins@divideai.demo", role: "morador" },
];
