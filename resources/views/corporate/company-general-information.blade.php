@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR: Tabs + Actions --}}
        <div class="flex items-center gap-3 px-4 py-4">
            {{-- Tabs --}}
    

            <div class="flex-1"></div>

            {{-- Actions (UI only) --}}
            <div class="flex items-center gap-2">
                <a href="#"
                   class="h-9 px-4 rounded-full border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 3a2 2 0 012-2h6a2 2 0 012 2v5a1 1 0 11-2 0V3H5v14h8v-3a1 1 0 112 0v3a2 2 0 01-2 2H5a2 2 0 01-2-2V3z"/>
                        <path d="M13 9a1 1 0 011 1v2.586l.293-.293a1 1 0 011.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 111.414-1.414l.293.293V10a1 1 0 011-1z"/>
                    </svg>
                    Download PDF
                </a>

                <button class="w-9 h-9 rounded-full border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 5h14a1 1 0 110 2H3a1 1 0 110-2zm0 4h14a1 1 0 110 2H3a1 1 0 110-2zm0 4h14a1 1 0 110 2H3a1 1 0 110-2z" clip-rule="evenodd"/>
                    </svg>
                </button>

                <button class="w-9 h-9 rounded-full border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>

                <div class="flex items-center">
                    <button class="px-4 h-9 rounded-l-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                        <span class="text-lg leading-none">+</span>
                        Contact
                    </button>
                    <button class="w-10 h-9 rounded-r-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center border-l border-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <button class="w-9 h-9 rounded-full border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- GIS-LIKE FORM VIEW (REAL TABLE + INPUTS) --}}
        <div class="p-4">
            <div class="border border-gray-200 rounded-lg bg-white overflow-hidden">

                {{-- Scroll container like document --}}
                <div class="h-[760px] overflow-auto bg-gray-50 p-6">
                    <div class="mx-auto max-w-[980px] bg-white border border-gray-300">

                        {{-- Title --}}
                        <div class="px-6 pt-6 text-center">
                            <div class="text-[13px] font-bold tracking-wide text-gray-900">GENERAL INFORMATION SHEET (GIS)</div>
                            <div class="text-[11px] font-semibold text-gray-900 mt-1">FOR THE YEAR <span class="px-2 border-b border-gray-400">2024</span></div>
                            <div class="text-[11px] font-semibold text-gray-900 mt-1">STOCK CORPORATION</div>
                        </div>

                        {{-- Instructions --}}
                        <div class="px-6 mt-4">
                            <div class="border border-gray-400">
                                <div class="px-3 py-2 text-[10px] leading-4 text-gray-900">
                                    <div class="font-bold">GENERAL INSTRUCTIONS:</div>
                                    <ol class="list-decimal pl-4 mt-1 space-y-1">
                                        <li>FOR USER CORPORATION: THIS GIS SHOULD BE SUBMITTED WITHIN THIRTY (30) CALENDAR DAYS FROM THE DATE OF THE ANNUAL STOCKHOLDERS' MEETING. DO NOT LEAVE ANY ITEM BLANK. WRITE “N.A.” IF THE INFORMATION REQUIRED IS NOT APPLICABLE.</li>
                                        <li>IF NO MEETING IS HELD, THE CORPORATION SHALL SUBMIT THE GIS NOT LATER THAN JANUARY 30 OF THE FOLLOWING YEAR.</li>
                                        <li>THIS GIS SHALL BE ACCOMPLISHED IN ENGLISH AND CERTIFIED AND SWORN TO BY THE CORPORATE SECRETARY OF THE CORPORATION.</li>
                                        <li>SUBMIT FOUR (4) COPIES OF THE GIS TO THE RECEIVING SECTION AT THE SEC MAIN OFFICE OR SATELLITE OFFICES.</li>
                                        <li>ONLY THE GIS ACCOMPLISHED IN ACCORDANCE WITH THESE INSTRUCTIONS SHALL BE CONSIDERED AS HAVING BEEN FILED.</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="text-center text-[10px] font-semibold text-gray-800 my-2">
                                PLEASE PRINT LEGIBLY
                            </div>
                        </div>

                        {{-- Main form grid --}}
                        <div class="px-6 pb-6">
                            {{-- Helper style: all inputs look like form text inside boxes --}}
                            <style>
                                .gis-cell { border: 1px solid #6b7280; } /* gray-500 */
                                .gis-label { font-size: 10px; font-weight: 700; letter-spacing: .02em; }
                                .gis-input {
                                    width: 100%;
                                    border: none;
                                    outline: none;
                                    padding: 6px 8px;
                                    font-size: 11px;
                                    line-height: 1.1rem;
                                    background: transparent;
                                }
                                .gis-input::placeholder { color: #9ca3af; } /* gray-400 */
                                .gis-subtext { font-size: 10px; color: #374151; } /* gray-700 */
                            </style>

                            {{-- Row 1: Corporate Name + Date Registered --}}
                            <div class="grid grid-cols-12 gap-0">
                                <div class="col-span-8 gis-cell">
                                    <div class="px-2 pt-1 gis-label">CORPORATE NAME:</div>
                                    <input class="gis-input" placeholder="Enter corporate name" value="">
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="px-2 pt-1 gis-label">DATE REGISTERED:</div>
                                    <input class="gis-input" placeholder="MM/DD/YYYY" value="">
                                </div>
                            </div>

                            {{-- Row 2: Business/Trade Name + Fiscal Year End --}}
                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-8 gis-cell">
                                    <div class="px-2 pt-1 gis-label">BUSINESS/TRADE NAME:</div>
                                    <input class="gis-input" placeholder="Enter trade name" value="">
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="px-2 pt-1 gis-label">FISCAL YEAR END:</div>
                                    <input class="gis-input" placeholder="e.g. December 31" value="">
                                </div>
                            </div>

                            {{-- Row 3: SEC Reg No + TIN --}}
                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-8 gis-cell">
                                    <div class="px-2 pt-1 gis-label">SEC REGISTRATION NUMBER:</div>
                                    <input class="gis-input" placeholder="Enter SEC registration number" value="">
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="px-2 pt-1 gis-label">CORPORATE TAX IDENTIFICATION NUMBER (TIN):</div>
                                    <input class="gis-input" placeholder="Enter TIN or N/A" value="">
                                </div>
                            </div>

                            {{-- Row 4: Annual meeting per by-laws + Website --}}
                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-8 gis-cell">
                                    <div class="px-2 pt-1 gis-label">DATE OF ANNUAL MEETING PER BY-LAWS:</div>
                                    <input class="gis-input" placeholder="e.g. Last Tuesday of November" value="">
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="px-2 pt-1 gis-label">WEBSITE/URL ADDRESS:</div>
                                    <input class="gis-input" placeholder="https://..." value="">
                                </div>
                            </div>

                            {{-- Row 5: Actual date + Email --}}
                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-8 gis-cell">
                                    <div class="px-2 pt-1 gis-label">ACTUAL DATE OF ANNUAL MEETING:</div>
                                    <input class="gis-input" placeholder="Enter actual meeting date" value="">
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="px-2 pt-1 gis-label">E-MAIL ADDRESS:</div>
                                    <input class="gis-input" placeholder="name@company.com" value="">
                                </div>
                            </div>

                            {{-- Row 6: Principal office address + Fax --}}
                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-8 gis-cell">
                                    <div class="px-2 pt-1 gis-label">COMPLETE PRINCIPAL OFFICE ADDRESS:</div>
                                    <input class="gis-input" placeholder="Enter full address" value="">
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="px-2 pt-1 gis-label">FAX NUMBER:</div>
                                    <input class="gis-input" placeholder="Enter fax or N/A" value="">
                                </div>
                            </div>

                            {{-- Row 7: Complete business address (full width) --}}
                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-12 gis-cell">
                                    <div class="px-2 pt-1 gis-label">COMPLETE BUSINESS ADDRESS:</div>
                                    <input class="gis-input" placeholder="Enter complete business address" value="">
                                </div>
                            </div>

                            {{-- Row 8: Emails + Mobile numbers (4 columns) --}}
                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">OFFICIAL E-MAIL ADDRESS</div>
                                    <input class="gis-input" placeholder="official@email.com" value="">
                                </div>
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">ALTERNATE E-MAIL ADDRESS</div>
                                    <input class="gis-input" placeholder="alternate@email.com" value="">
                                </div>
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">OFFICIAL MOBILE NUMBER</div>
                                    <input class="gis-input" placeholder="09xx..." value="">
                                </div>
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">ALTERNATE MOBILE NUMBER</div>
                                    <input class="gis-input" placeholder="09xx... or N/A" value="">
                                </div>
                            </div>

                            {{-- Row 9: External auditor + SEC accreditation + Telephone --}}
                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-6 gis-cell">
                                    <div class="px-2 pt-1 gis-label">NAME OF EXTERNAL AUDITOR & ITS SIGNING PARTNER:</div>
                                    <input class="gis-input" placeholder="Enter auditor name" value="">
                                </div>
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">SEC ACCREDITATION NUMBER (if applicable):</div>
                                    <input class="gis-input" placeholder="Enter number or N/A" value="">
                                </div>
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">TELEPHONE NUMBER(S):</div>
                                    <input class="gis-input" placeholder="Enter telephone" value="">
                                </div>
                            </div>

                            {{-- Row 10: Purpose + Industry + Geo code --}}
                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-6 gis-cell">
                                    <div class="px-2 pt-1 gis-label">PRIMARY PURPOSE/ACTIVITY/INDUSTRY PRESENTLY ENGAGED IN:</div>
                                    <textarea class="gis-input" rows="4" placeholder="Describe purpose/activity..."></textarea>
                                </div>
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">INDUSTRY CLASSIFICATION:</div>
                                    <input class="gis-input" placeholder="e.g. Management Services" value="">
                                </div>
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">GEOGRAPHICAL CODE:</div>
                                    <input class="gis-input" placeholder="e.g. 6000" value="">
                                </div>
                            </div>

                            {{-- Intercompany affiliations header --}}
                            <div class="gis-cell -mt-px">
                                <div class="text-center text-[10px] font-bold py-2 tracking-wide">
                                    INTERCOMPANY AFFILIATIONS
                                </div>
                            </div>

                            {{-- Table header: Parent company --}}
                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-4 gis-cell">
                                    <div class="text-center text-[10px] font-bold py-2">PARENT COMPANY</div>
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="text-center text-[10px] font-bold py-2">SEC REGISTRATION NO.</div>
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="text-center text-[10px] font-bold py-2">ADDRESS</div>
                                </div>
                            </div>

                            {{-- Parent company row (editable) --}}
                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-4 gis-cell"><input class="gis-input text-center" placeholder="N/A or Name" value=""></div>
                                <div class="col-span-4 gis-cell"><input class="gis-input text-center" placeholder="N/A" value=""></div>
                                <div class="col-span-4 gis-cell"><input class="gis-input text-center" placeholder="N/A" value=""></div>
                            </div>

                            {{-- Subsidiary header --}}
                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-4 gis-cell">
                                    <div class="text-center text-[10px] font-bold py-2">SUBSIDIARY / AFFILIATE</div>
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="text-center text-[10px] font-bold py-2">SEC REGISTRATION NO.</div>
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="text-center text-[10px] font-bold py-2">ADDRESS</div>
                                </div>
                            </div>

                            {{-- Subsidiary row 1 --}}
                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-4 gis-cell"><input class="gis-input text-center" placeholder="N/A or Name" value=""></div>
                                <div class="col-span-4 gis-cell"><input class="gis-input text-center" placeholder="N/A" value=""></div>
                                <div class="col-span-4 gis-cell"><input class="gis-input text-center" placeholder="N/A" value=""></div>
                            </div>

                            {{-- Subsidiary row 2 (extra) --}}
                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-4 gis-cell"><input class="gis-input text-center" placeholder="(optional)" value=""></div>
                                <div class="col-span-4 gis-cell"><input class="gis-input text-center" placeholder="(optional)" value=""></div>
                                <div class="col-span-4 gis-cell"><input class="gis-input text-center" placeholder="(optional)" value=""></div>
                            </div>

                            {{-- Footer note --}}
                            <div class="gis-cell -mt-px">
                                <div class="text-center text-[10px] py-2 text-gray-700 font-semibold">
                                    NOTE: USE ADDITIONAL SHEET IF NECESSARY
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- Small hint footer (optional) --}}
            <div class="mt-3 text-xs text-gray-400">
                Static UI first — inputs are ready so later you can bind database values per tab.
            </div>
        </div>

    </div>

</div>
@endsection