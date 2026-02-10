<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member ID Card - {{ $member->full_name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Century Gothic", "CenturyGothic", "AppleGothic", Arial, sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .identity-card {
            width: 3.375in; /* Standard ID card size */
            height: 2.125in;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #1e3c72 100%);
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
            color: white;
            border: 3px solid #fff;
        }
        
        .card-header {
            background: rgba(255,255,255,0.15);
            padding: 8px 12px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.3);
            backdrop-filter: blur(10px);
        }
        
        .church-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }
        
        .church-subtitle {
            font-size: 9px;
            opacity: 0.9;
            font-weight: 500;
        }
        
        .card-body {
            padding: 12px;
            display: flex;
            height: calc(100% - 50px);
        }
        
        .photo-section {
            width: 60px;
            height: 60px;
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .photo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .photo i {
            font-size: 24px;
            opacity: 0.7;
        }
        
        .info-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .qr-section {
            width: 50px;
            height: 60px;
            margin-left: 8px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .qr-code {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 4px;
            padding: 2px;
        }
        
        .qr-code img {
            width: 100%;
            height: 100%;
            border-radius: 2px;
        }
        
        .qr-code canvas {
            width: 100%;
            height: 100%;
            border-radius: 2px;
        }
        
        .qr-code {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .print-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        .print-button i {
            margin-right: 8px;
        }
        
        .download-button {
            position: fixed;
            top: 20px;
            right: 150px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .download-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        .download-button i {
            margin-right: 8px;
        }
        
        .member-name {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 4px;
            line-height: 1.2;
        }
        
        .member-id {
            font-size: 10px;
            opacity: 0.9;
            margin-bottom: 2px;
        }
        
        .member-type {
            font-size: 9px;
            background: rgba(255,255,255,0.2);
            padding: 2px 6px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 4px;
        }
        
        .member-details {
            font-size: 8px;
            line-height: 1.3;
        }
        
        .detail-row {
            margin-bottom: 2px;
        }
        
        .detail-label {
            font-weight: bold;
            opacity: 0.8;
        }
        
        .detail-value {
            opacity: 0.9;
        }
        
        .decorative-elements {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }
        
        .decorative-elements::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 80px;
            height: 80px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .decorative-elements::after {
            content: '';
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        
        /* Print styles - Force override with !important */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            body {
                background: white !important;
                padding: 20px !important;
                margin: 0 !important;
            }
            
            .print-button,
            .download-button {
                display: none !important;
            }
            
            .identity-card {
                margin: 0 !important;
                box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
                border: 3px solid #fff !important;
                background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #1e3c72 100%) !important;
                color: white !important;
                width: 3.375in !important;
                height: 2.125in !important;
                border-radius: 12px !important;
                position: relative !important;
                overflow: hidden !important;
                font-family: "Century Gothic", "CenturyGothic", "AppleGothic", Arial, sans-serif !important;
            }
            
            .card-header {
                background: rgba(255,255,255,0.15) !important;
                padding: 8px 12px !important;
                text-align: center !important;
                border-bottom: 1px solid rgba(255,255,255,0.3) !important;
            }
            
            .church-name {
                font-size: 14px !important;
                font-weight: bold !important;
                margin-bottom: 2px !important;
                color: white !important;
            }
            
            .church-subtitle {
                font-size: 9px !important;
                opacity: 0.9 !important;
                color: white !important;
            }
            
            .card-body {
                padding: 12px !important;
                display: flex !important;
                height: calc(100% - 50px) !important;
            }
            
            .photo-section {
                width: 60px !important;
                height: 60px !important;
                margin-right: 12px !important;
            }
            
            .photo {
                width: 60px !important;
                height: 60px !important;
                border-radius: 50% !important;
                border: 2px solid rgba(255,255,255,0.3) !important;
                background: rgba(255,255,255,0.1) !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                overflow: hidden !important;
            }
            
            .photo img {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                border-radius: 50% !important;
            }
            
            .info-section {
                flex: 1 !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
            }
            
            .member-name {
                font-size: 13px !important;
                font-weight: bold !important;
                margin-bottom: 4px !important;
                color: white !important;
            }
            
            .member-id {
                font-size: 10px !important;
                opacity: 0.9 !important;
                margin-bottom: 2px !important;
                color: white !important;
            }
            
            .member-type {
                font-size: 9px !important;
                background: rgba(255,255,255,0.2) !important;
                padding: 2px 6px !important;
                border-radius: 10px !important;
                display: inline-block !important;
                margin-bottom: 4px !important;
                color: white !important;
            }
            
            .member-details {
                font-size: 8px !important;
                line-height: 1.3 !important;
                color: white !important;
            }
            
            .detail-row {
                margin-bottom: 2px !important;
            }
            
            .detail-label {
                font-weight: bold !important;
                opacity: 0.8 !important;
                color: white !important;
            }
            
            .detail-value {
                opacity: 0.9 !important;
                color: white !important;
            }
            
            .qr-section {
                width: 50px !important;
                height: 60px !important;
                margin-left: 8px !important;
                flex-shrink: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            
            .qr-code {
                width: 50px !important;
                height: 50px !important;
                background: white !important;
                border-radius: 4px !important;
                padding: 2px !important;
            }
            
            .qr-code canvas,
            .qr-code img {
                width: 100% !important;
                height: 100% !important;
                border-radius: 2px !important;
            }
            
            .decorative-elements {
                position: absolute !important;
                top: -50px !important;
                right: -50px !important;
                width: 100px !important;
                height: 100px !important;
                background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%) !important;
                border-radius: 50% !important;
            }
            
            @page {
                size: A4;
                margin: 0.5in;
            }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .identity-card {
                width: 90%;
                max-width: 3.375in;
                height: auto;
                min-height: 2.125in;
            }
            
            .print-button,
            .download-button {
                position: relative;
                top: auto;
                right: auto;
                margin: 10px;
                display: inline-block;
            }
        }
    </style>
</head>
<body>
    
    <button class="print-button" onclick="window.print()">
        <i class="fas fa-print"></i> Print ID Card
    </button>
    
    <button class="download-button" onclick="downloadCard()">
        <i class="fas fa-download"></i> Print Card
    </button>
    
    <div class="identity-card">
        <div class="decorative-elements"></div>
        
        <div class="card-header">
            <div class="church-name">KKKT USHIRIKA WA LONGUO</div>
            <div class="church-subtitle">Member Identity Card</div>
        </div>
        
        <div class="card-body">
            <div class="photo-section">
                <div class="photo">
                    @if($member->profile_picture)
                        <img src="{{ asset('storage/' . $member->profile_picture) }}" 
                             alt="Profile" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                    @else
                        <i class="fas fa-user"></i>
                    @endif
                </div>
            </div>
            
            <div class="info-section">
                <div>
                    <div class="member-name">{{ $member->full_name ?? 'Unknown Member' }}</div>
                    <div class="member-id">ID: {{ $member->member_id ?? 'N/A' }}</div>
                    <div class="member-type">
                        {{ ucfirst($member->membership_type ?? 'Member') }} 
                        @if($member->member_type)
                            - {{ ucfirst($member->member_type) }}
                        @endif
                    </div>
                </div>
                
                <div class="member-details">
                    @if($member->phone_number)
                        <div class="detail-row">
                            <span class="detail-label">Phone:</span> 
                            <span class="detail-value">{{ $member->phone_number }}</span>
                        </div>
                    @endif
                    
                    @if($member->region)
                        <div class="detail-row">
                            <span class="detail-label">Region:</span> 
                            <span class="detail-value">{{ $member->region }}</span>
                        </div>
                    @endif
                    
                    <div class="detail-row">
                        <span class="detail-label">Issued:</span> 
                        <span class="detail-value">{{ now()->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="qr-section">
                <div class="qr-code" id="memberQrCode"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        // Generate QR code for member
        document.addEventListener('DOMContentLoaded', function() {
            // Comprehensive member data for QR code (same as member details modal)
            const qrData = `Full Name: {{ $member->full_name ?? '-' }}
Member ID: {{ $member->member_id ?? '-' }}
Membership Type: {{ $member->membership_type ?? '-' }}
Member Type: {{ $member->member_type ?? '-' }}
Phone: {{ $member->phone_number ?? '-' }}
Email: {{ $member->email ?? '-' }}
Gender: {{ $member->gender ? ucfirst($member->gender) : '-' }}
Date of Birth: {{ $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('M d, Y') : '-' }}
Education Level: {{ $member->education_level ?? '-' }}
Profession: {{ $member->profession ?? '-' }}
NIDA Number: {{ $member->nida_number ?? '-' }}
Region: {{ $member->region ?? '-' }}
District: {{ $member->district ?? '-' }}
Ward: {{ $member->ward ?? '-' }}
Street: {{ $member->street ?? '-' }}
Address: {{ $member->address ?? '-' }}
Living with family: {{ $member->living_with_family ?? '-' }}
Family relationship: {{ $member->family_relationship ?? '-' }}
Tribe: {{ ($member->tribe ?? '-') . ($member->other_tribe ? ' (' . $member->other_tribe . ')' : '') }}@if($member->membership_type === 'temporary' || ($member->membership_type === 'permanent' && $member->member_type === 'independent'))
Guardian Name: {{ $member->guardian_name ?? '-' }}
Guardian Phone: {{ $member->guardian_phone ?? '-' }}
Guardian Relationship: {{ $member->guardian_relationship ?? '-' }}@endif
Church: KKKT USHIRIKA WA LONGUO
Issued: {{ now()->format('M d, Y') }}`;
            
            const qrElement = document.getElementById('memberQrCode');
            if (qrElement) {
                // Try to generate QR code with library first
                function tryLibraryQR() {
                    if (typeof QRCode !== 'undefined') {
                        QRCode.toCanvas(qrElement, qrData, {
                            width: 46,
                            height: 46,
                            margin: 1,
                            color: {
                                dark: '#000000',
                                light: '#FFFFFF'
                            }
                        }, function (error) {
                            if (error) {
                                console.error('QR Code library error:', error);
                                useExternalQR();
                            }
                        });
                    } else {
                        // Library not loaded, use external service
                        useExternalQR();
                    }
                }
                
                // Fallback to external QR code service
                function useExternalQR() {
                    const encodedData = encodeURIComponent(qrData);
                    const qrImg = document.createElement('img');
                    qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=46x46&data=${encodedData}`;
                    qrImg.style.width = '100%';
                    qrImg.style.height = '100%';
                    qrImg.style.borderRadius = '2px';
                    qrImg.alt = 'Member QR Code';
                    
                    qrImg.onload = function() {
                        qrElement.innerHTML = '';
                        qrElement.appendChild(qrImg);
                    };
                    
                    qrImg.onerror = function() {
                        qrElement.innerHTML = '<div style="font-size: 8px; text-align: center; padding: 10px; color: #666;">QR Code<br/>Not Available</div>';
                    };
                }
                
                // Wait a bit for library to load, then try
                setTimeout(tryLibraryQR, 500);
            }
        });
        
        function downloadCard() {
            // Simple approach - just open print dialog
            window.print();
        }
    </script>
</body>
</html>
