import { useState } from 'react'
import {
useAccount,
useConnect,
useDisconnect,
useReadContract,
useWriteContract,
useWaitForTransactionReceipt,
} from 'wagmi'
import { injected } from 'wagmi/connectors'
import { formatEther, maxUint256 } from 'viem'
import { BDNameServiceABI } from './abi'

const CONTRACT_ADDRESS = '0x4981657B5365D54F7096B3e15B95dB07cc8A2E56' as `0x${string}`
const BDC_TOKEN = '0x92A4D9448Afe3DfEe7d8b3fa2CdD604eF5f3262F' as `0x${string}`

const ERC20_ABI = [
{
name: 'approve',
type: 'function',
stateMutability: 'nonpayable',
inputs: [
{ name: 'spender', type: 'address' },
{ name: 'amount', type: 'uint256' },
],
outputs: [{ type: 'bool' }],
},
{
name: 'allowance',
type: 'function',
stateMutability: 'view',
inputs: [
{ name: 'owner', type: 'address' },
{ name: 'spender', type: 'address' },
],
outputs: [{ type: 'uint256' }],
},
] as const

export default function App() {
const { address, isConnected } = useAccount()
const { connect } = useConnect()
const { disconnect } = useDisconnect()
const [name, setName] = useState('')
const [years, setYears] = useState(1)
const { data: isAvailable, isLoading: checkingAvailable } = useReadContract({
address: CONTRACT_ADDRESS,
abi: BDNameServiceABI,
functionName: 'isAvailable',
args: [name],
query: { enabled: name.length > 0 },
})
const { data: owner1 } = useReadContract({
    address: CONTRACT_ADDRESS,
    abi: BDNameServiceABI,
    functionName: 'getOwner',
    args: ['mrahmanrumel'],
  })

  const { data: expiry1 } = useReadContract({
    address: CONTRACT_ADDRESS,
    abi: BDNameServiceABI,
    functionName: 'getExpiry',
    args: ['mrahmanrumel'],
  })

  const { data: owner2 } = useReadContract({
    address: CONTRACT_ADDRESS,
    abi: BDNameServiceABI,
    functionName: 'getOwner',
    args: ['fenchuganj'],
  })

  const { data: expiry2 } = useReadContract({
    address: CONTRACT_ADDRESS,
    abi: BDNameServiceABI,
    functionName: 'getExpiry',
    args: ['fenchuganj'],
  })
const { data: price, isLoading: checkingPrice } = useReadContract({
address: CONTRACT_ADDRESS,
abi: BDNameServiceABI,
functionName: 'getPrice',
args: [name, BigInt(years)],
query: { enabled: name.length > 0 },
})

const { data: allowance } = useReadContract({
address: BDC_TOKEN,
abi: ERC20_ABI,
functionName: 'allowance',
args: address ? [address, CONTRACT_ADDRESS] : undefined,
query: { enabled: !!address },
})

const { writeContract, data: hash, isPending } = useWriteContract()
const { isLoading: isConfirming, isSuccess } = useWaitForTransactionReceipt({ hash })

const needsApproval = price !== undefined && allowance !== undefined && allowance < price

const handleApprove = () => {
writeContract({
address: BDC_TOKEN,
abi: ERC20_ABI,
functionName: 'approve',
args: [CONTRACT_ADDRESS, maxUint256],
})
}

const handleRegister = () => {
if (!name || !isAvailable) return
writeContract({
address: CONTRACT_ADDRESS,
abi: BDNameServiceABI,
functionName: 'register',
args: [name, BigInt(years)],
})
}

return (
<div style={{
minHeight: '100vh',
background: 'linear-gradient(160deg, #0f172a 0%, #1e3a8a 50%, #0f172a 100%)',
color: 'white',
fontFamily: '-apple-system, BlinkMacSystemFont, sans-serif',
padding: '20px',
}}>
<div style={{ maxWidth: '480px', margin: '0 auto' }}>
<div style={{
display: 'flex',
justifyContent: 'space-between',
alignItems: 'center',
marginBottom: '32px',
paddingTop: '10px',
}}>
<div>
<h1 style={{ fontSize: '24px', margin: 0, fontWeight: 700 }}>BD Name Service</h1>
<p style={{ margin: '4px 0 0', opacity: 0.6, fontSize: '13px' }}>.bd Domains on Base</p>
</div>
{isConnected ? (
<button
onClick={() => disconnect()}
style={{
background: 'rgba(255,255,255,0.1)',
border: '1px solid rgba(255,255,255,0.2)',
color: 'white',
padding: '10px 16px',
borderRadius: '12px',
fontSize: '13px',
cursor: 'pointer',
}}
>
{address?.slice(0, 6)}...{address?.slice(-4)}
</button>
) : (
<button
onClick={() => connect({ connector: injected() })}
style={{
background: '#2563eb',
border: 'none',
color: 'white',
padding: '10px 18px',
borderRadius: '12px',
fontWeight: 600,
fontSize: '14px',
cursor: 'pointer',
}}
>
Connect Wallet
</button>
)}
</div>

<div style={{
background: 'rgba(255,255,255,0.06)',
borderRadius: '24px',
padding: '24px',
border: '1px solid rgba(255,255,255,0.1)',
}}>
<div style={{ position: 'relative', marginBottom: '14px' }}>
<input
type="text"
placeholder="Search for a name"
value={name}
onChange={(e) => setName(e.target.value.toLowerCase().replace(/[^a-z0-9-]/g, ''))}
style={{
width: '100%',
padding: '16px 50px 16px 16px',
borderRadius: '16px',
border: 'none',
background: 'rgba(0,0,0,0.35)',
color: 'white',
fontSize: '17px',
outline: 'none',
boxSizing: 'border-box',
}}
/>
<span style={{
position: 'absolute',
right: '16px',
top: '50%',
transform: 'translateY(-50%)',
opacity: 0.5,
fontSize: '16px',
}}>.bd</span>
</div>

<select
value={years}
onChange={(e) => setYears(Number(e.target.value))}
style={{
width: '100%',
padding: '14px 16px',
borderRadius: '14px',
border: 'none',
background: 'rgba(0,0,0,0.35)',
color: 'white',
fontSize: '15px',
marginBottom: '18px',
outline: 'none',
}}
>
{[1, 2, 3, 5, 10].map((y) => (
<option key={y} value={y}>{y} Year{y > 1 ? 's' : ''}</option>
))}
</select>

{name.length > 0 && (
<div style={{
background: 'rgba(0,0,0,0.3)',
borderRadius: '18px',
padding: '18px',
border: '1px solid rgba(255,255,255,0.08)',
}}>
<div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
<div>
<p style={{ fontSize: '22px', fontWeight: 700, margin: '0 0 6px' }}>
{name}<span style={{ color: '#60a5fa' }}>.bd</span>
</p>
<p style={{ margin: 0, fontSize: '14px' }}>
{checkingAvailable ? (
<span style={{ opacity: 0.6 }}>Checking...</span>
) : isAvailable ? (
<span style={{ color: '#4ade80' }}>Available</span>
) : (
<span style={{ color: '#f87171' }}>Already taken</span>
)}
</p>
</div>
<div style={{ textAlign: 'right' }}>
{checkingPrice ? (
<p style={{ margin: 0, opacity: 0.6 }}>...</p>
) : price !== undefined ? (
<>
<p style={{ fontSize: '20px', fontWeight: 700, margin: 0 }}>
{Number(formatEther(price)).toLocaleString()}
</p>
<p style={{ margin: 0, fontSize: '12px', opacity: 0.6 }}>BDC</p>
</>
) : null}
</div>
</div>

{isConnected && isAvailable && (
needsApproval ? (
<button
onClick={handleApprove}
disabled={isPending || isConfirming}
style={{
width: '100%',
marginTop: '18px',
padding: '15px',
borderRadius: '14px',
border: 'none',
background: 'linear-gradient(90deg, #f59e0b, #f97316)',
color: 'white',
fontWeight: 700,
fontSize: '16px',
cursor: 'pointer',
opacity: isPending || isConfirming ? 0.7 : 1,
}}
>
{isPending || isConfirming ? 'Approving...' : 'Approve BDC'}
</button>
) : (
<button
onClick={handleRegister}
disabled={isPending || isConfirming}
style={{
width: '100%',
marginTop: '18px',
padding: '15px',
borderRadius: '14px',
border: 'none',
background: 'linear-gradient(90deg, #2563eb, #06b6d4)',
color: 'white',
fontWeight: 700,
fontSize: '16px',
cursor: 'pointer',
opacity: isPending || isConfirming ? 0.7 : 1,
}}
>
{isPending || isConfirming ? 'Confirming...' : isSuccess ? 'Registered!' : 'Register Now'}
</button>
)
)}

{!isConnected && isAvailable && (
<p style={{ marginTop: '16px', textAlign: 'center', fontSize: '14px', opacity: 0.6 }}>
Connect wallet to register
</p>
)}{isConnected && (
          <div style={{
            marginTop: '20px',
            background: 'rgba(255,255,255,0.06)',
            borderRadius: '20px',
            padding: '20px',
            border: '1px solid rgba(255,255,255,0.1)',
          }}>
            <p style={{ margin: '0 0 12px', fontWeight: 600, fontSize: '16px' }}>My Domains</p>
            {owner1 && address && String(owner1).toLowerCase() === address.toLowerCase() && (
              <div style={{ marginBottom: '10px', padding: '12px', background: 'rgba(0,0,0,0.3)', borderRadius: '12px' }}>
                <p style={{ margin: 0, fontWeight: 600 }}>mrahmanrumel<span style={{ color: '#60a5fa' }}>.bd</span></p>
                <p style={{ margin: '4px 0 0', fontSize: '12px', opacity: 0.6 }}>
                  Expires: {expiry1 ? new Date(Number(expiry1) * 1000).toLocaleDateString() : '...'}
                </p>
              </div>
            )}
            {owner2 && address && String(owner2).toLowerCase() === address.toLowerCase() && (
              <div style={{ padding: '12px', background: 'rgba(0,0,0,0.3)', borderRadius: '12px' }}>
                <p style={{ margin: 0, fontWeight: 600 }}>fenchuganj<span style={{ color: '#60a5fa' }}>.bd</span></p>
                <p style={{ margin: '4px 0 0', fontSize: '12px', opacity: 0.6 }}>
                  Expires: {expiry2 ? new Date(Number(expiry2) * 1000).toLocaleDateString() : '...'}
                </p>
              </div>
            )}
          </div>
        )}
</div>
)}
</div>

<div style={{
marginTop: '20px',
padding: '14px',
background: 'rgba(255,255,255,0.04)',
borderRadius: '16px',
fontSize: '12px',
opacity: 0.7,
}}>
<p style={{ margin: '0 0 4px' }}>Contract on Base</p>
<p style={{ margin: 0, wordBreak: 'break-all', fontSize: '11px' }}>{CONTRACT_ADDRESS}</p>
</div>

<p style={{ textAlign: 'center', marginTop: '30px', opacity: 0.4, fontSize: '12px' }}>
Powered by BDCoin • Built on Base
</p>
</div>
</div>
)
}
