import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
    children?: Array;
    permission?: string
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
}

export interface User {
    id: string;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    roles?: Roles[];
}

export interface Roles {
    id: number;
    name: string;
    description: string;
    level: number;
}


export interface LocationImage {
    id: number
    image: string
}

export interface Location {
    id: string
    name: string
    address: string
    min_participants: number
    images?: LocationImage[]
}

export interface TimeSlot {
    id: number;
    location_id: string;
    date: string;      // YYYY-MM-DD
    start_time: string; // HH:mm
    end_time: string;   // HH:mm
    is_available: boolean;
    location: Location; // Aqui vem a localização do time slot
}


export interface Document {
    id: number
    file_path: string
    type: string
    status: string
    status_label: string
    type_label: string
}

export interface User {
    id: number
    name: string
    email: string
    cpf: string
    rg: string
    zip_code: string
    address: string
    neighborhood: string
    number: string
    complement?: string
    city: string
    state: string
    country: string
}


export interface Appointment {
    id: number;              
    user_id: string;          
    notes: string;           
    created_at?: string;
    updated_at?: string;
    time_slots: TimeSlot[];
    participants?: Participant[];
    user: User;               
}

interface Participant {
    id: number;
    name: string;
    cpf: string;
    rg: string;
    contact?: Nullable<string>;
    email?: Nullable<string>;
}

export type BreadcrumbItemType = BreadcrumbItem;
