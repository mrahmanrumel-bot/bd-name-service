import { http, createConfig } from 'wagmi'
import { base } from 'wagmi/chains'
import { injected, walletConnect } from 'wagmi/connectors'

export const config = createConfig({
  chains: [base],
  connectors: [
    injected(),
    walletConnect({
      projectId: 'd1fbe7d6234d98c7ee9813d2ae267339',
      metadata: {
        name: 'BD Name Service',
        description: '.bd names on Base',
        url: 'https://bd-name-service-psi.vercel.app',
        icons: ['https://bd-name-service-psi.vercel.app/favicon.ico'],
      },
      showQrModal: true,
    }),
  ],
  transports: {
    [base.id]: http(),
  },
})