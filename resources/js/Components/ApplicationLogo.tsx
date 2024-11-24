import { SVGAttributes } from 'react';

export default function ApplicationLogo(props: SVGAttributes<SVGElement>) {
    return (
        <div className="flex items-center space-x-2">
            <svg className="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23,11H13V1c0-0.6-0.4-1-1-1s-1,0.4-1,1v10H1c-0.6,0-1,0.4-1,1s0.4,1,1,1h10v10c0,0.6,0.4,1,1,1s1-0.4,1-1V13h10c0.6,0,1-0.4,1-1S23.6,11,23,11z"/>
            </svg>
            <span className="text-xl font-bold">OpenComunidad</span>
        </div>
    );
}
