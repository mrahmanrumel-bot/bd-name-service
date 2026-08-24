export const BDNameServiceABI = [
{
inputs: [
{ internalType: "string", name: "name", type: "string" },
{ internalType: "uint256", name: "durationYears", type: "uint256" }
],
name: "register",
outputs: [],
stateMutability: "nonpayable",
type: "function"
},
{
inputs: [{ internalType: "string", name: "name", type: "string" }],
name: "isAvailable",
outputs: [{ internalType: "bool", name: "", type: "bool" }],
stateMutability: "view",
type: "function"
},
{
inputs: [
{ internalType: "string", name: "name", type: "string" },
{ internalType: "uint256", name: "durationYears", type: "uint256" }
],
name: "getPrice",
outputs: [{ internalType: "uint256", name: "", type: "uint256" }],
stateMutability: "view",
type: "function"
},
{
inputs: [{ internalType: "string", name: "name", type: "string" }],
name: "getOwner",
outputs: [{ internalType: "address", name: "", type: "address" }],
stateMutability: "view",
type: "function"
}
] as const;