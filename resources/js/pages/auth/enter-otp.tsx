import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

type EnterOTP = {
    otp: string;
};

export default function EnterOTP() {
    const { data, setData, post, processing, errors, reset } = useForm<Required<EnterOTP>>({
        otp: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('verify-otp'));
    };

    return (
        <AuthLayout title="Verify your OTP" description="Enter your OTP below to that you received on your email">
            <Head title="Log in" />

            <form className="flex flex-col gap-6" onSubmit={submit}>
                <div className="grid gap-6">
                    <div className="grid gap-2">
                        <Label htmlFor="otp">One-Time Password</Label>
                        <Input
                            id="otp"
                            type="text"
                            required
                            autoFocus
                            tabIndex={1}
                            value={data.otp}
                            onChange={(e) => setData('otp', e.target.value)}
                            placeholder="Enter your OTP"
                        />
                        <InputError message={errors.otp} />
                    </div>

                    <Button type="submit" className="mt-4 w-full" tabIndex={4} disabled={processing}>
                        {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                        Log in
                    </Button>
                </div>
            </form>

            {status && <div className="mb-4 text-center text-sm font-medium text-green-600">{status}</div>}
        </AuthLayout>
    );
}
